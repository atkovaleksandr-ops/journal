@extends('layouts.app')

@section('content')
<div class="container">
    <div class="page-header">
        <div>
            <h1 class="page-title">Моя посещаемость</h1>
            <p class="page-subtitle">{{ $student->last_name }} {{ $student->first_name }} · {{ $student->group->name ?? 'Группа не указана' }}</p>
        </div>
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">В кабинет</a>
    </div>

    @if($subjectSummaries->count() > 0)
        <div class="dashboard-grid">
            @foreach($subjectSummaries as $item)
                <a href="{{ route('student.attendance.subject', $item['subject']) }}" class="dashboard-card">
                    <h3>{{ $item['subject']->name }}</h3>
                    <p>{{ $item['subject']->description ?: 'История посещения уроков по предмету.' }}</p>
                    <div class="mini-stat-row"><span>Всего уроков</span><strong>{{ $item['lessons_count'] }}</strong></div>
                    <div class="mini-stat-row"><span>Присутствовал</span><strong>{{ $item['present_count'] }}</strong></div>
                    <div class="mini-stat-row"><span>Отсутствовал</span><strong>{{ $item['absent_count'] }}</strong></div>
                    <div class="mini-stat-row"><span>Не отмечено</span><strong>{{ $item['not_marked_count'] }}</strong></div>
                    <div class="mini-stat-row"><span>Посещаемость</span><strong>{{ $item['attendance_percent'] !== null ? $item['attendance_percent'].'%' : '—' }}</strong></div>
                    <span class="card-link">Открыть историю</span>
                </a>
            @endforeach
        </div>
    @else
        <div class="empty-state">Для вашей группы пока нет предметов и уроков.</div>
    @endif
</div>
@endsection
