<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;

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
        $group->delete();

        return redirect()
            ->route('groups.index')
            ->with('success', 'Группа удалена.');
    }
}
