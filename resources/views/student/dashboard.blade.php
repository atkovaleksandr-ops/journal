@extends('layouts.app')

@section('content')
<div class="container">
    <div class="page-header">
        <div>
            <h1 class="page-title">Личный кабинет</h1>
            <p class="page-subtitle">Добро пожаловать, <strong>{{ auth()->user()->name }}</strong>. Здесь можно проверить посещаемость по каждому предмету.</p>
        </div>
        @if($student)<div class="badge">{{ $student->group->name ?? 'Группа не указана' }}</div>@endif
    </div>

    @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

    @if($student)
        <div class="stat-grid">
            <div class="stat-card"><span class="stat-value">{{ $subjectsCount }}</span><span class="stat-label">Предметы</span></div>
            <div class="stat-card"><span class="stat-value">{{ $lessonsCount }}</span><span class="stat-label">Уроки</span></div>
            <div class="stat-card"><span class="stat-value">{{ $presentCount }}</span><span class="stat-label">Присутствовал</span></div>
            <div class="stat-card"><span class="stat-value">{{ $absentCount }}</span><span class="stat-label">Отсутствовал</span></div>
            <div class="stat-card"><span class="stat-value">{{ $attendancePercent !== null ? $attendancePercent.'%' : '—' }}</span><span class="stat-label">Посещаемость</span></div>
        </div>
        <div class="dashboard-grid">
            <a href="{{ route('student.attendance.history') }}" class="dashboard-card">
                <h3>История посещаемости</h3>
                <p>Выберите предмет и посмотрите, какие занятия были посещены, пропущены или ещё не отмечены преподавателем.</p>
                <span class="card-link">Открыть посещаемость</span>
            </a>
        </div>
    @else
        <div class="empty-state">Аккаунт пока не привязан к студенту. Обратитесь к преподавателю или администратору.</div>
    @endif
</div>
@endsection
