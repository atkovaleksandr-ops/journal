@extends('layouts.app')

@section('content')
<div class="container">
    @php
        $statusLabels = ['present' => 'Присутствовал', 'absent' => 'Отсутствовал', 'not_marked' => 'Не отмечено'];
    @endphp

    <div class="page-header">
        <div>
            <h1 class="page-title">{{ $subject->name }}</h1>
            <p class="page-subtitle">{{ $student->last_name }} {{ $student->first_name }} · {{ $student->group->name ?? 'Группа не указана' }}</p>
        </div>
        <a href="{{ route('student.attendance.history') }}" class="btn btn-secondary">К предметам</a>
    </div>

    <div class="stat-grid compact-stats">
        <div class="stat-card"><span class="stat-value">{{ $summary['lessons'] }}</span><span class="stat-label">Всего уроков</span></div>
        <div class="stat-card"><span class="stat-value">{{ $summary['present'] }}</span><span class="stat-label">Присутствовал</span></div>
        <div class="stat-card"><span class="stat-value">{{ $summary['absent'] }}</span><span class="stat-label">Отсутствовал</span></div>
        <div class="stat-card"><span class="stat-value">{{ $summary['not_marked'] }}</span><span class="stat-label">Не отмечено</span></div>
        <div class="stat-card"><span class="stat-value">{{ $summary['attendance_percent'] !== null ? $summary['attendance_percent'].'%' : '—' }}</span><span class="stat-label">Посещаемость</span></div>
    </div>

    <form action="{{ route('student.attendance.subject', $subject) }}" method="GET" class="filter-panel">
        <div class="field"><label for="date_from">С даты</label><input id="date_from" type="date" name="date_from" value="{{ $filters['date_from'] }}"></div>
        <div class="field"><label for="date_to">По дату</label><input id="date_to" type="date" name="date_to" value="{{ $filters['date_to'] }}"></div>
        <div class="field">
            <label for="status">Статус</label>
            <select id="status" name="status">
                <option value="">Все уроки</option>
                @foreach($statusLabels as $value => $label)<option value="{{ $value }}" @selected($filters['status'] === $value)>{{ $label }}</option>@endforeach
            </select>
        </div>
        <div class="field field-grow"><label for="q">Поиск</label><input id="q" type="search" name="q" value="{{ $filters['q'] }}" placeholder="Тема или описание урока"></div>
        <div class="field">
            <label for="sort">Сортировка</label>
            <select id="sort" name="sort"><option value="date_desc" @selected($filters['sort'] === 'date_desc')>Новые сверху</option><option value="date_asc" @selected($filters['sort'] === 'date_asc')>Старые сверху</option></select>
        </div>
        <div class="filter-actions"><button type="submit" class="btn btn-primary">Найти</button><a href="{{ route('student.attendance.subject', $subject) }}" class="btn btn-secondary">Сбросить</a></div>
    </form>

    @if($lessons->count())
        <div class="panel">
            <table class="responsive-table">
                <thead><tr><th>Дата</th><th>Тема урока</th><th>Посещаемость</th><th>Заметка</th></tr></thead>
                <tbody>
                    @foreach($lessons as $lesson)
                        @php
                            $attendance = $lesson->attendances->first();
                            $status = $attendance?->status ?? 'not_marked';
                            $status = in_array($status, ['present', 'absent'], true) ? $status : 'not_marked';
                        @endphp
                        <tr>
                            <td data-label="Дата">{{ \Illuminate\Support\Carbon::parse($lesson->date)->format('d.m.Y') }}</td>
                            <td data-label="Тема"><strong>{{ $lesson->topic ?: 'Без темы' }}</strong>@if($lesson->description)<div class="muted">{{ $lesson->description }}</div>@endif</td>
                            <td data-label="Посещаемость"><span class="badge {{ $status === 'present' ? 'badge-success' : ($status === 'absent' ? 'badge-warning-soft' : '') }}">{{ $statusLabels[$status] }}</span></td>
                            <td data-label="Заметка">{{ $attendance?->note ?: '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="empty-state">По выбранным условиям уроки не найдены.</div>
    @endif
</div>
@endsection
