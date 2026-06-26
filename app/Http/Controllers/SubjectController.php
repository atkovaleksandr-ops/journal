<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SubjectController extends Controller
{
    /**
     * Список предметов текущего учителя
     */
    public function index(Request $request)
    {
        $filters = [
            'group_id' => $request->query('group_id'),
            'q' => trim((string) $request->query('q', '')),
            'sort' => $request->query('sort', 'name'),
        ];

        $subjectsQuery = Subject::with('group')
            ->withCount('lessons')
            ->where('teacher_id', auth()->id())
            ->when($filters['group_id'], fn ($query) => $query->where('group_id', $filters['group_id']))
            ->when($filters['q'] !== '', function ($query) use ($filters) {
                $query->where(function ($searchQuery) use ($filters) {
                    $searchQuery->where('name', 'like', "%{$filters['q']}%")
                        ->orWhere('description', 'like', "%{$filters['q']}%");
                });
            });

        match ($filters['sort']) {
            'group' => $subjectsQuery->orderBy(
                Group::select('name')
                    ->whereColumn('groups.id', 'subjects.group_id')
                    ->limit(1)
            )
                ->orderBy('subjects.name'),
            'lessons' => $subjectsQuery->orderByDesc('lessons_count')->orderBy('name'),
            'newest' => $subjectsQuery->latest(),
            default => $subjectsQuery->orderBy('name'),
        };

        $subjects = $subjectsQuery->get();
        $this->attachCoTeachers($subjects);
        $groups = Group::orderBy('name')->get();

        return view('subjects.index', compact('subjects', 'groups', 'filters'));
    }

    /**
     * Форма создания предмета
     */
    public function create()
    {
        $groups = Group::orderBy('name')->get();
        $existingAssignments = Subject::query()
            ->with(['teacher:id,name', 'group:id,name'])
            ->orderBy('name')
            ->get(['id', 'name', 'group_id', 'teacher_id']);

        return view('subjects.create', compact('groups', 'existingAssignments'));
    }

    /**
     * Сохранение нового предмета
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'group_id' => ['required', 'exists:groups,id'],
            'description' => ['nullable', 'string'],
        ]);

        $validated['name'] = $this->cleanSubjectName($validated['name']);
        $this->ensureTeacherAssignmentIsUnique($validated['name'], $validated['group_id']);

        $coTeachers = $this->matchingAssignments($validated['name'], $validated['group_id'])
            ->where('teacher_id', '!=', auth()->id())
            ->pluck('teacher.name')
            ->filter()
            ->unique()
            ->values();

        Subject::create([
            'name' => $validated['name'],
            'group_id' => $validated['group_id'],
            'description' => $validated['description'] ?? null,
            'teacher_id' => auth()->id(),
        ]);

        return redirect()
            ->route('subjects.index')
            ->with(
                'success',
                $coTeachers->isEmpty()
                    ? 'Предмет успешно создан.'
                    : 'Предмет создан. Эту же дисциплину в группе также ведут: '.$coTeachers->join(', ').'. Журналы преподавателей остаются раздельными.'
            );
    }

    /**
     * Просмотр одного предмета
     */
    public function show(Subject $subject)
    {
        if ($subject->teacher_id !== auth()->id()) {
            abort(403);
        }

        return view('subjects.show', compact('subject'));
    }

    /**
     * Форма редактирования предмета
     */
    public function edit(Subject $subject)
    {
        if ($subject->teacher_id !== auth()->id()) {
            abort(403);
        }

        $groups = Group::orderBy('name')->get();
        $existingAssignments = Subject::query()
            ->with(['teacher:id,name', 'group:id,name'])
            ->whereKeyNot($subject->id)
            ->orderBy('name')
            ->get(['id', 'name', 'group_id', 'teacher_id']);

        return view('subjects.edit', compact('subject', 'groups', 'existingAssignments'));
    }

    /**
     * Обновление предмета
     */
    public function update(Request $request, Subject $subject)
    {
        if ($subject->teacher_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'group_id' => ['required', 'exists:groups,id'],
            'description' => ['nullable', 'string'],
        ]);

        $validated['name'] = $this->cleanSubjectName($validated['name']);
        $this->ensureTeacherAssignmentIsUnique(
            $validated['name'],
            $validated['group_id'],
            $subject->id
        );

        $subject->update($validated);

        return redirect()
            ->route('subjects.index')
            ->with('success', 'Предмет обновлён.');
    }

    /**
     * Удаление предмета
     */
    public function destroy(Subject $subject)
    {
        if ($subject->teacher_id !== auth()->id()) {
            abort(403);
        }

        $subject->delete();

        return redirect()
            ->route('subjects.index')
            ->with('success', 'Предмет удалён.');
    }

    private function ensureTeacherAssignmentIsUnique(string $name, int|string $groupId, ?int $ignoreId = null): void
    {
        $normalizedName = $this->subjectNameKey($name);

        $duplicate = Subject::query()
            ->where('teacher_id', auth()->id())
            ->where('group_id', $groupId)
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->get(['id', 'name'])
            ->contains(fn (Subject $subject) => $this->subjectNameKey($subject->name) === $normalizedName);

        if ($duplicate) {
            throw ValidationException::withMessages([
                'name' => 'У вас уже есть этот предмет в выбранной группе.',
            ]);
        }
    }

    private function matchingAssignments(string $name, int|string $groupId)
    {
        $normalizedName = $this->subjectNameKey($name);

        return Subject::query()
            ->with('teacher:id,name')
            ->where('group_id', $groupId)
            ->get()
            ->filter(fn (Subject $subject) => $this->subjectNameKey($subject->name) === $normalizedName)
            ->values();
    }

    private function attachCoTeachers($subjects): void
    {
        $groupIds = $subjects->pluck('group_id')->filter()->unique();

        if ($groupIds->isEmpty()) {
            return;
        }

        $assignments = Subject::query()
            ->with('teacher:id,name')
            ->whereIn('group_id', $groupIds)
            ->where('teacher_id', '!=', auth()->id())
            ->get()
            ->groupBy(fn (Subject $subject) => $this->assignmentKey($subject->name, $subject->group_id));

        $subjects->each(function (Subject $subject) use ($assignments) {
            $subject->setRelation(
                'coTeachers',
                $assignments
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
        return $this->subjectNameKey($name).'|'.$groupId;
    }

    private function cleanSubjectName(string $name): string
    {
        return trim((string) preg_replace('/\s+/u', ' ', trim($name)));
    }

    private function subjectNameKey(string $name): string
    {
        return mb_strtolower($this->cleanSubjectName($name));
    }
}
