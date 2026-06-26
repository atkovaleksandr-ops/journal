<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class TeacherController extends Controller
{
    public function index(Request $request): View
    {
        $filters = [
            'q' => trim((string) $request->query('q', '')),
            'sort' => $request->query('sort', 'newest'),
        ];

        $teachersQuery = User::where('role', 'teacher')
            ->withCount(['subjects', 'lessons'])
            ->when($filters['q'] !== '', function ($query) use ($filters) {
                $query->where(function ($searchQuery) use ($filters) {
                    $searchQuery->where('name', 'like', "%{$filters['q']}%")
                        ->orWhere('email', 'like', "%{$filters['q']}%");
                });
            });

        match ($filters['sort']) {
            'name' => $teachersQuery->orderBy('name'),
            'subjects' => $teachersQuery->orderByDesc('subjects_count')->orderBy('name'),
            'lessons' => $teachersQuery->orderByDesc('lessons_count')->orderBy('name'),
            default => $teachersQuery->latest(),
        };

        $teachers = $teachersQuery->get();

        return view('admin.teachers.index', compact('teachers', 'filters'));
    }

    public function create(): View
    {
        return view('admin.teachers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'login_password' => $validated['password'],
            'role' => 'teacher',
        ]);

        return redirect()
            ->route('admin.teachers.index')
            ->with('success', 'Преподаватель создан. Теперь он может войти и работать с группами, студентами и предметами.');
    }

    public function show(User $teacher): View
    {
        $this->ensureTeacher($teacher);

        $teacher->loadCount(['subjects', 'lessons']);

        $subjects = $teacher->subjects()
            ->with('group')
            ->withCount('lessons')
            ->orderBy('name')
            ->get();

        $this->attachCoTeachers($subjects, $teacher);

        $recentLessons = $teacher->lessons()
            ->with(['group', 'subject'])
            ->latest('date')
            ->limit(8)
            ->get();

        return view('admin.teachers.show', compact('teacher', 'subjects', 'recentLessons'));
    }

    public function edit(User $teacher): View
    {
        $this->ensureTeacher($teacher);

        return view('admin.teachers.edit', compact('teacher'));
    }

    public function update(Request $request, User $teacher): RedirectResponse
    {
        $this->ensureTeacher($teacher);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($teacher->id),
            ],
        ]);

        $teacher->update($validated);

        return redirect()
            ->route('admin.teachers.show', $teacher)
            ->with('success', 'Данные преподавателя обновлены.');
    }

    public function resetPassword(Request $request, User $teacher): RedirectResponse
    {
        $this->ensureTeacher($teacher);

        $validated = $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $teacher->update([
            'password' => Hash::make($validated['password']),
            'login_password' => $validated['password'],
        ]);

        return redirect()
            ->route('admin.teachers.edit', $teacher)
            ->with('success', 'Пароль преподавателя обновлен.');
    }

    public function destroy(User $teacher): RedirectResponse
    {
        $this->ensureTeacher($teacher);

        $teacher->delete();

        return redirect()
            ->route('admin.teachers.index')
            ->with('success', 'Преподаватель удален.');
    }

    private function ensureTeacher(User $teacher): void
    {
        abort_unless($teacher->role === 'teacher', 404);
    }

    private function attachCoTeachers($subjects, User $teacher): void
    {
        $groupIds = $subjects->pluck('group_id')->filter()->unique();

        if ($groupIds->isEmpty()) {
            return;
        }

        $otherAssignments = Subject::query()
            ->with('teacher')
            ->whereIn('group_id', $groupIds)
            ->where('teacher_id', '!=', $teacher->id)
            ->get()
            ->groupBy(fn (Subject $subject) => $this->assignmentKey($subject->name, $subject->group_id));

        $subjects->each(function (Subject $subject) use ($otherAssignments) {
            $subject->setRelation(
                'coTeachers',
                $otherAssignments
                    ->get($this->assignmentKey($subject->name, $subject->group_id), collect())
                    ->pluck('teacher')
                    ->filter()
                    ->unique('id')
                    ->values()
            );
        });
    }

    private function assignmentKey(string $name, int|string|null $groupId): string
    {
        return mb_strtolower(trim($name)).'|'.$groupId;
    }
}
