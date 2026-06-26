@extends('layouts.app')

@section('content')
<div class="container">
    <div class="page-header">
        <div>
            <h1 class="page-title">Кабинет преподавателя</h1>
            <p class="page-subtitle">Добро пожаловать, <strong>{{ auth()->user()->name }}</strong>. Выберите нужный раздел для работы.</p>
        </div>
    </div>

    <div class="stat-grid">
        <div class="stat-card">
            <span class="stat-value">{{ $groupsCount ?? 0 }}</span>
            <span class="stat-label">Группы</span>
        </div>
        <div class="stat-card">
            <span class="stat-value">{{ $studentsCount ?? 0 }}</span>
            <span class="stat-label">Студенты</span>
        </div>
        <div class="stat-card">
            <span class="stat-value">{{ $subjectsCount ?? 0 }}</span>
            <span class="stat-label">Мои предметы</span>
        </div>
        <div class="stat-card">
            <span class="stat-value">{{ $lessonsCount ?? 0 }}</span>
            <span class="stat-label">Проведенные уроки</span>
        </div>
    </div>

    <div class="dashboard-grid">
        <a href="{{ route('groups.index') }}" class="dashboard-card">
            <h3>Группы</h3>
            <p>Список учебных групп, состав студентов и переход к журналу посещаемости.</p>
        </a>

        <a href="{{ route('students.index') }}" class="dashboard-card">
            <h3>Студенты</h3>
            <p>Добавление студентов, привязка к группам и связь с аккаунтом по email.</p>
        </a>

        <a href="{{ route('subjects.index') }}" class="dashboard-card">
            <h3>Предметы</h3>
            <p>Предметы преподавателя, описания и привязка к учебным группам.</p>
        </a>
    </div>
</div>
@endsection
