@extends('layouts.app')

@section('content')
<div class="container">
    <div class="page-header">
        <div>
            <h1 class="page-title">Группы</h1>
            <p class="page-subtitle">Быстрый доступ к составу, предметам и журналу каждой учебной группы.</p>
        </div>

        <a href="{{ route('groups.create') }}" class="btn btn-success">Создать группу</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('groups.index') }}" method="GET" class="filter-panel">
        <div class="field field-grow">
            <label for="q">Поиск</label>
            <input id="q" type="search" name="q" value="{{ $filters['q'] }}" placeholder="Название или описание">
        </div>

        <div class="field">
            <label for="sort">Сортировка</label>
            <select id="sort" name="sort">
                <option value="name" @selected($filters['sort'] === 'name')>По названию</option>
                <option value="students" @selected($filters['sort'] === 'students')>Больше студентов</option>
                <option value="subjects" @selected($filters['sort'] === 'subjects')>Больше предметов</option>
                <option value="lessons" @selected($filters['sort'] === 'lessons')>Больше уроков</option>
                <option value="newest" @selected($filters['sort'] === 'newest')>Сначала новые</option>
            </select>
        </div>

        <div class="filter-actions">
            <button type="submit" class="btn btn-primary">Показать</button>
            <a href="{{ route('groups.index') }}" class="btn btn-secondary">Сбросить</a>
        </div>
    </form>

    @if($groups->count() > 0)
        <div class="teacher-card-grid">
            @foreach($groups as $group)
                <article class="teacher-card">
                    <div class="teacher-card-top">
                        <span class="badge">{{ $group->students_count }} студентов</span>
                        <span class="muted">{{ $group->subjects_count }} предметов · {{ $group->lessons_count }} уроков</span>
                    </div>

                    <h2>{{ $group->name }}</h2>
                    <p>{{ $group->description ?: 'Описание пока не добавлено.' }}</p>

                    <div class="lesson-actions">
                        <a href="{{ route('groups.attendance', $group) }}" class="btn btn-primary">Журнал</a>
                        <a href="{{ route('students.index', ['group_id' => $group->id]) }}" class="btn btn-secondary">Студенты</a>
                        <a href="{{ route('groups.show', $group) }}" class="btn btn-secondary">Карточка</a>
                    </div>
                </article>
            @endforeach
        </div>
    @else
        <div class="empty-state">Группы по выбранным условиям не найдены.</div>
    @endif
</div>
@endsection
