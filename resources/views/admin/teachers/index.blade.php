@extends('layouts.app')

@section('content')
<div class="container">
    <div class="page-header">
        <div>
            <h1 class="page-title">Учителя</h1>
            <p class="page-subtitle">Администратор выдает доступ преподавателям и контролирует их учебную нагрузку.</p>
        </div>

        <a href="{{ route('admin.teachers.create') }}" class="btn btn-success">Создать учителя</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('admin.teachers.index') }}" method="GET" class="filter-panel">
        <div class="field field-grow">
            <label for="q">Поиск</label>
            <input id="q" type="search" name="q" value="{{ $filters['q'] }}" placeholder="Имя или email">
        </div>

        <div class="field">
            <label for="sort">Сортировка</label>
            <select id="sort" name="sort">
                <option value="newest" @selected($filters['sort'] === 'newest')>Сначала новые</option>
                <option value="name" @selected($filters['sort'] === 'name')>По имени</option>
                <option value="subjects" @selected($filters['sort'] === 'subjects')>По предметам</option>
                <option value="lessons" @selected($filters['sort'] === 'lessons')>По урокам</option>
            </select>
        </div>

        <div class="filter-actions">
            <button type="submit" class="btn btn-primary">Показать</button>
            <a href="{{ route('admin.teachers.index') }}" class="btn btn-secondary">Сбросить</a>
        </div>
    </form>

    @if($teachers->count() > 0)
        <div class="teacher-card-grid">
            @foreach($teachers as $teacher)
                <article class="teacher-card">
                    <div class="teacher-card-top">
                        <span class="badge">{{ $teacher->subjects_count }} предметов</span>
                        <span class="muted">{{ $teacher->lessons_count }} уроков</span>
                    </div>

                    <h2>{{ $teacher->name }}</h2>
                    <p>{{ $teacher->email }}</p>
                    <p class="muted">
                        {{ $teacher->login_password ? 'Пароль доступен администратору' : 'Требуется обновить пароль для просмотра' }}
                    </p>
                    <p class="muted">Создан {{ $teacher->created_at->format('d.m.Y') }}</p>

                    <div class="lesson-actions">
                        <a href="{{ route('admin.teachers.show', $teacher, false) }}" class="btn btn-primary">Открыть</a>
                        <a href="{{ route('admin.teachers.edit', $teacher, false) }}" class="btn btn-secondary">Изменить</a>
                        <form action="{{ route('admin.teachers.destroy', $teacher) }}" method="POST" onsubmit="return confirm('Удалить учителя?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Удалить</button>
                        </form>
                    </div>
                </article>
            @endforeach
        </div>
    @else
        <div class="empty-state">Учителя по выбранным условиям не найдены.</div>
    @endif
</div>
@endsection
