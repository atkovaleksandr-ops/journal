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
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        Group::create($validated);

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
        return view('groups.edit', compact('group'));
    }

    /**
     * Обновление группы
     */
    public function update(Request $request, Group $group)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $group->update($validated);

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
}
