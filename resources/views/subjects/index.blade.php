@extends('layouts.app')

@section('content')
<div class="container">
    <div class="page-header">
        <div>
            <h1 class="page-title">Предметы</h1>
            <p class="page-subtitle">Быстрый список предметов преподавателя: фильтр по группе, поиск и переход прямо в журнал.</p>
        </div>

        <a href="{{ route('subjects.create') }}" class="btn btn-success">Добавить предмет</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('subjects.index') }}" method="GET" class="filter-panel">
        <div class="field field-grow">
            <label for="q">Поиск</label>
            <input id="q" type="search" name="q" value="{{ $filters['q'] }}" placeholder="Название или описание">
        </div>

        <div class="field">
            <label for="group_id">Группа</label>
            <select id="group_id" name="group_id">
                <option value="">Все группы</option>
                @foreach($groups as $group)
                    <option value="{{ $group->id }}" @selected((string) $filters['group_id'] === (string) $group->id)>{{ $group->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="field">
            <label for="sort">Сортировка</label>
            <select id="sort" name="sort">
                <option value="name" @selected($filters['sort'] === 'name')>По названию</option>
                <option value="group" @selected($filters['sort'] === 'group')>По группе</option>
                <option value="lessons" @selected($filters['sort'] === 'lessons')>По количеству уроков</option>
                <option value="newest" @selected($filters['sort'] === 'newest')>Сначала новые</option>
            </select>
        </div>

        <div class="filter-actions">
            <button type="submit" class="btn btn-primary">Показать</button>
            <a href="{{ route('subjects.index') }}" class="btn btn-secondary">Сбросить</a>
        </div>
    </form>

    @if($subjects->count() > 0)
        <div class="teacher-card-grid">
            @foreach($subjects as $subject)
                <article class="teacher-card">
                    <div class="teacher-card-top">
                        <span class="badge">{{ $subject->group->name ?? 'Без группы' }}</span>
                        <span class="muted">{{ $subject->lessons_count }} уроков</span>
                    </div>

                    <h2>{{ $subject->name }}</h2>
                    <p>{{ $subject->description ?: 'Описание пока не добавлено.' }}</p>
                    @if($subject->coTeachers->isNotEmpty())
                        <p class="muted">Также ведут: {{ $subject->coTeachers->pluck('name')->join(', ') }}</p>
                    @endif

                    <div class="lesson-actions">
                        @if($subject->group)
                            <a href="{{ route('groups.attendance', ['group' => $subject->group_id, 'subject_id' => $subject->id, 'sort' => 'date_desc']) }}" class="btn btn-primary">Открыть журнал</a>
                        @endif

                        <a href="{{ route('subjects.show', $subject) }}" class="btn btn-secondary">Карточка</a>
                    </div>
                </article>
            @endforeach
        </div>
    @else
        <div class="empty-state">
            По выбранным условиям предметы не найдены. Сбросьте фильтр или добавьте новый предмет.
        </div>
    @endif
</div>
@endsection
