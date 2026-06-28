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

    @if($groups->count() > 0)
        <div class="teacher-card-grid">
            @foreach($groups as $group)
                @php
                    $studentsCount = (int) $group->students_count;
                    $studentsMod100 = $studentsCount % 100;
                    $studentsMod10 = $studentsCount % 10;
                    $studentsWord = $studentsMod100 >= 11 && $studentsMod100 <= 14
                        ? 'студентов'
                        : match ($studentsMod10) {
                            1 => 'студент',
                            2, 3, 4 => 'студента',
                            default => 'студентов',
                        };
                    $pluralWord = function (int $count, array $forms): string {
                        $mod100 = $count % 100;
                        $mod10 = $count % 10;

                        return $mod100 >= 11 && $mod100 <= 14
                            ? $forms[2]
                            : match ($mod10) {
                                1 => $forms[0],
                                2, 3, 4 => $forms[1],
                                default => $forms[2],
                            };
                    };
                    $subjectsWord = $pluralWord((int) $group->subjects_count, ['предмет', 'предмета', 'предметов']);
                    $lessonsWord = $pluralWord((int) $group->lessons_count, ['урок', 'урока', 'уроков']);
                @endphp
                <article class="teacher-card">
                    <div class="teacher-card-top">
                        <span class="group-count-badge">
                            <strong>{{ $studentsCount }}</strong>
                            <span>{{ $studentsWord }}</span>
                        </span>
                        <span class="muted">{{ $group->subjects_count }} {{ $subjectsWord }} · {{ $group->lessons_count }} {{ $lessonsWord }}</span>
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
