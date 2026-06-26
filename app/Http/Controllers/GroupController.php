<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Group;
use App\Models\Lesson;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class GroupController extends Controller
{
    /**
     * Список групп
     */
    public function index(Request $request)
    {
        $filters = [
            'q' => trim((string) $request->query('q', '')),
            'sort' => $request->query('sort', 'name'),
        ];

        $groupsQuery = Group::withCount(['students', 'subjects', 'lessons'])
            ->when($filters['q'] !== '', function ($query) use ($filters) {
                $query->where(function ($searchQuery) use ($filters) {
                    $searchQuery->where('name', 'like', "%{$filters['q']}%")
                        ->orWhere('description', 'like', "%{$filters['q']}%");
                });
            });

        match ($filters['sort']) {
            'students' => $groupsQuery->orderByDesc('students_count')->orderBy('name'),
            'subjects' => $groupsQuery->orderByDesc('subjects_count')->orderBy('name'),
            'lessons' => $groupsQuery->orderByDesc('lessons_count')->orderBy('name'),
            'newest' => $groupsQuery->latest(),
            default => $groupsQuery->orderBy('name'),
        };

        $groups = $groupsQuery->get();

        return view('groups.index', compact('groups', 'filters'));
    }

    /**
     * Форма создания группы
     */
    public function create()
    {
        return view('groups.create');
    }

    /**
     * Сохранение новой группы
     */
    public function store(Request $request)
    {
        $payload = $this->groupPayload($this->validateGroupIdentity($request));
        $this->ensureGroupNameIsUnique($payload['name']);

        Group::create($payload);

        return redirect()
            ->route('groups.index')
            ->with('success', 'Группа успешно создана.');
    }

    /**
     * Просмотр одной группы
     */
    public function show(Group $group)
    {
        $group->load('students', 'subjects');

        return view('groups.show', compact('group'));
    }

    /**
     * Форма редактирования группы
     */
    public function edit(Group $group)
    {
        $groupIdentity = $this->groupIdentityFrom($group);

        return view('groups.edit', compact('group', 'groupIdentity'));
    }

    /**
     * Обновление группы
     */
    public function update(Request $request, Group $group)
    {
        $payload = $this->groupPayload($this->validateGroupIdentity($request));
        $this->ensureGroupNameIsUnique($payload['name'], $group->id);

        $group->update($payload);

        return redirect()
            ->route('groups.index')
            ->with('success', 'Группа обновлена.');
    }

    /**
     * Удаление группы
     */
    public function destroy(Group $group)
    {
        $group->load(['students.user', 'subjects', 'lessons']);

        DB::transaction(function () use ($group) {
            $studentIds = $group->students->pluck('id');
            $subjectIds = $group->subjects->pluck('id');
            $lessonIds = $group->lessons->pluck('id');
            $studentUserIds = $group->students
                ->pluck('user')
                ->filter(fn ($user) => $user && $user->role === 'student')
                ->pluck('id');

            if ($studentIds->isNotEmpty() || $subjectIds->isNotEmpty() || $lessonIds->isNotEmpty()) {
                Attendance::query()
                    ->where(function ($query) use ($studentIds, $subjectIds, $lessonIds) {
                        if ($studentIds->isNotEmpty()) {
                            $query->whereIn('student_id', $studentIds);
                        }

                        if ($subjectIds->isNotEmpty()) {
                            $method = $studentIds->isNotEmpty() ? 'orWhereIn' : 'whereIn';
                            $query->{$method}('subject_id', $subjectIds);
                        }

                        if ($lessonIds->isNotEmpty()) {
                            $method = $studentIds->isNotEmpty() || $subjectIds->isNotEmpty() ? 'orWhereIn' : 'whereIn';
                            $query->{$method}('lesson_id', $lessonIds);
                        }
                    })
                    ->delete();
            }

            Lesson::whereIn('id', $lessonIds)->delete();
            Subject::whereIn('id', $subjectIds)->delete();
            Student::whereIn('id', $studentIds)->delete();
            User::whereIn('id', $studentUserIds)->delete();
            $group->delete();
        });

        return redirect()
            ->route('groups.index')
            ->with('success', 'Группа удалена.');
    }

    private function validateGroupIdentity(Request $request): array
    {
        return $request->validate([
            'program_name' => ['required', 'string', 'max:255'],
            'course' => ['required', 'integer', 'min:1', 'max:6'],
            'group_number' => ['required', 'integer', 'min:1', 'max:9'],
        ]);
    }

    private function groupPayload(array $validated): array
    {
        $programName = $this->cleanProgramName($validated['program_name']);
        $course = (int) $validated['course'];
        $groupNumber = (int) $validated['group_number'];

        return [
            'name' => $this->shortGroupName($programName, $course, $groupNumber),
            'description' => "{$programName}, {$course} курс, {$groupNumber} группа",
        ];
    }

    private function ensureGroupNameIsUnique(string $name, ?int $ignoreId = null): void
    {
        $exists = Group::query()
            ->where('name', $name)
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'group_number' => "Группа {$name} уже существует. Измените курс или номер группы.",
            ]);
        }
    }

    private function groupIdentityFrom(Group $group): array
    {
        $programName = $group->description ?: $group->name;
        $course = null;
        $groupNumber = null;

        if (preg_match('/^(.*?),\s*(\d+)\s*курс(?:,\s*(\d+)\s*группа)?/u', (string) $group->description, $matches)) {
            $programName = trim($matches[1]);
            $course = (int) $matches[2];
            $groupNumber = isset($matches[3]) ? (int) $matches[3] : null;
        }

        if (preg_match('/-(\d)(\d)$/u', $group->name, $matches)) {
            $course ??= (int) $matches[1];
            $groupNumber ??= (int) $matches[2];
        }

        return [
            'program_name' => $this->cleanProgramName($programName),
            'course' => $course ?: 1,
            'group_number' => $groupNumber ?: 1,
        ];
    }

    private function shortGroupName(string $programName, int $course, int $groupNumber): string
    {
        $words = preg_split('/[\s\-]+/u', $this->cleanProgramName($programName), -1, PREG_SPLIT_NO_EMPTY);
        $initials = collect($words)
            ->map(fn (string $word) => mb_strtoupper(mb_substr($word, 0, 1)))
            ->join('');

        return "{$initials}-{$course}{$groupNumber}";
    }

    private function cleanProgramName(string $value): string
    {
        return trim((string) preg_replace('/\s+/u', ' ', $value));
    }
}
