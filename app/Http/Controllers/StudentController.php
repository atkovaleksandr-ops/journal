<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Student;
use App\Models\User;
use App\Services\AttendanceSummaryService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    private const DEFAULT_STUDENT_PASSWORD = 'student123';

    /**
     * Список студентов
     */
    public function index(Request $request)
    {
        $filters = [
            'group_id' => $request->query('group_id'),
            'q' => trim((string) $request->query('q', '')),
            'sort' => $request->query('sort', 'name'),
        ];

        $studentsQuery = Student::with('group')
            ->withCount('attendances')
            ->when($filters['group_id'], fn ($query) => $query->where('group_id', $filters['group_id']))
            ->when($filters['q'] !== '', function ($query) use ($filters) {
                $query->where(function ($searchQuery) use ($filters) {
                    $searchQuery->where('first_name', 'like', "%{$filters['q']}%")
                        ->orWhere('last_name', 'like', "%{$filters['q']}%")
                        ->orWhere('student_number', 'like', "%{$filters['q']}%")
                        ->orWhere('email', 'like', "%{$filters['q']}%");
                });
            });

        match ($filters['sort']) {
            'group' => $studentsQuery->orderBy(
                Group::select('name')
                    ->whereColumn('groups.id', 'students.group_id')
                    ->limit(1)
            )->orderBy('last_name')->orderBy('first_name'),
            'number' => $studentsQuery->orderBy('student_number')->orderBy('last_name'),
            'activity' => $studentsQuery->orderByDesc('attendances_count'),
            'newest' => $studentsQuery->latest(),
            default => $studentsQuery->orderBy('last_name')->orderBy('first_name'),
        };

        $students = $studentsQuery->get();
        $groups = Group::withCount('students')->orderBy('name')->get();
        $studentsByGroup = $students->groupBy(fn ($student) => $student->group->name ?? 'Без группы');

        return view('students.index', compact('students', 'groups', 'studentsByGroup', 'filters'));
    }

    /**
     * Форма создания студента
     */
    public function create()
    {
        $groups = Group::orderBy('name')->get();

        return view('students.create', compact('groups'));
    }

    /**
     * Сохранение студента
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'student_number' => ['nullable', 'string', 'max:255', 'unique:students,student_number'],
            'email' => ['nullable', 'email', 'max:255', 'unique:students,email', 'unique:users,email'],
            'login_password' => ['nullable', 'string', 'min:6', 'max:255'],
            'group_id' => ['required', 'exists:groups,id'],
        ]);

        $password = $validated['login_password'] ?: self::DEFAULT_STUDENT_PASSWORD;
        $validated['login_password'] = $password;

        $student = Student::create($validated);

        $this->syncStudentUser($student, $password);

        return redirect()
            ->route('students.index')
            ->with('success', 'Студент успешно добавлен.');
    }

    /**
     * Просмотр студента
     */
    public function show(Student $student, AttendanceSummaryService $attendanceSummaryService)
    {
        $student->load('group', 'attendances.subject', 'attendances.lesson', 'user');
        $attendanceData = $attendanceSummaryService->forStudent($student, withLessonHistory: true);
        $subjectSummaries = $attendanceData['subjectSummaries'];
        $attendanceSummary = $attendanceData['summary'];

        return view('students.show', compact('student', 'subjectSummaries', 'attendanceSummary'));
    }

    /**
     * Форма редактирования студента
     */
    public function edit(Student $student)
    {
        $student->loadMissing('user');
        $groups = Group::orderBy('name')->get();
        $studentPassword = $student->login_password ?: $student->user?->login_password;

        return view('students.edit', compact('student', 'groups', 'studentPassword'));
    }

    /**
     * Обновление студента
     */
    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'student_number' => ['nullable', 'string', 'max:255', 'unique:students,student_number,' . $student->id],
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('students', 'email')->ignore($student->id),
                Rule::unique('users', 'email')->ignore($student->user_id),
            ],
            'login_password' => ['nullable', 'string', 'min:6', 'max:255'],
            'group_id' => ['required', 'exists:groups,id'],
        ]);

        $password = $validated['login_password'] ?? null;

        if ($password) {
            $validated['login_password'] = $password;
        } else {
            unset($validated['login_password']);
        }

        $student->update($validated);

        $this->syncStudentUser($student, $password);

        $returnTo = $this->safeReturnTo(
            $request->input('return_to'),
            route('students.index', [], false)
        );

        return redirect()->route('students.show', [
            'student' => $student->id,
            'return_to' => $returnTo,
        ])
            ->with('success', 'Студент обновлён.');
    }

    /**
     * Удаление студента
     */
    public function destroy(Request $request, Student $student)
    {
        if ($student->user && $student->user->role === 'student') {
            $student->user->delete();
        }

        $student->delete();

        return redirect($this->safeReturnTo(
            $request->input('return_to'),
            route('students.index', [], false)
        ))
            ->with('success', 'Студент удалён.');
    }

    private function syncStudentUser(Student $student, ?string $password = null): void
    {
        if (!$student->email) {
            if ($student->user && $student->user->role === 'student') {
                $student->user->delete();
            }

            if ($student->user_id) {
                $student->forceFill(['user_id' => null])->save();
            }

            return;
        }

        $user = $student->user;

        if (!$user) {
            $user = User::where('email', $student->email)
                ->where('role', 'student')
                ->first();
        }

        $payload = [
            'name' => trim($student->first_name . ' ' . $student->last_name),
            'email' => $student->email,
            'role' => 'student',
        ];

        if ($password) {
            $payload['password'] = Hash::make($password);
            $payload['login_password'] = $password;
        }

        if (!$user) {
            $payload['password'] = Hash::make($password ?: self::DEFAULT_STUDENT_PASSWORD);
            $payload['login_password'] = $password ?: self::DEFAULT_STUDENT_PASSWORD;
            $user = User::create($payload);
        } else {
            $user->update($payload);
        }

        if ($student->user_id !== $user->id) {
            $student->forceFill(['user_id' => $user->id])->save();
        }
    }

    private function safeReturnTo(?string $returnTo, string $fallback): string
    {
        if (!$returnTo) {
            return $fallback;
        }

        $host = parse_url($returnTo, PHP_URL_HOST);

        if ($host && $host !== request()->getHost()) {
            return $fallback;
        }

        return $returnTo;
    }
}
