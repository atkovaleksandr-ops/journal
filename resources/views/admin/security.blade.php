@extends('layouts.app')

@section('content')
<div class="container">
    <div class="page-header">
        <div>
            <h1 class="page-title">Доступы</h1>
            <p class="page-subtitle">Проверьте учетные записи студентов и назначение предметов преподавателям.</p>
        </div>

        <div class="section-actions">
            <a href="{{ route('dashboard', [], false) }}" class="btn btn-secondary">Назад</a>
        </div>
    </div>

    <div class="stat-grid">
        <div class="stat-card">
            <span class="stat-value">{{ $adminsCount ?? 0 }}</span>
            <span class="stat-label">Администраторы</span>
        </div>
        <div class="stat-card">
            <span class="stat-value">{{ $teachersCount ?? 0 }}</span>
            <span class="stat-label">Учителя</span>
        </div>
        <div class="stat-card">
            <span class="stat-value">{{ $studentsCount ?? 0 }}</span>
            <span class="stat-label">Студенты</span>
        </div>
        <div class="stat-card">
            <span class="stat-value">{{ $studentsWithoutLogin->count() }}</span>
            <span class="stat-label">Нужно выдать вход</span>
        </div>
        <div class="stat-card">
            <span class="stat-value">{{ $teachersWithoutSubjects->count() }}</span>
            <span class="stat-label">Нужно назначить предметы</span>
        </div>
    </div>

    <section class="panel" style="margin-top: 22px;">
        <div class="page-header" style="margin-bottom: 12px;">
            <div>
                <h2>Студенты без данных для входа</h2>
                <p class="page-subtitle">Проверьте студентов, которым еще нужно указать email или выдать пароль для личного кабинета.</p>
            </div>
        </div>

        @if($studentsWithoutLogin->isEmpty())
            <div class="empty-state">У всех студентов есть данные для входа.</div>
        @else
            <div class="table-wrap">
                <table class="responsive-table">
                    <thead>
                        <tr>
                            <th>Студент</th>
                            <th>Группа</th>
                            <th>Email</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($studentsWithoutLogin as $student)
                            <tr>
                                <td data-label="Студент">
                                    <strong>{{ $student->last_name }} {{ $student->first_name }}</strong><br>
                                    <span class="muted">{{ $student->student_number }}</span>
                                </td>
                                <td data-label="Группа">{{ $student->group->name ?? 'Без группы' }}</td>
                                <td data-label="Email">{{ $student->email ?: 'не указан' }}</td>
                                <td data-label="Действия">
                                    <div class="actions">
                                        <a href="{{ route('students.edit', $student, false) }}" class="btn btn-secondary btn-compact">Изменить</a>
                                        <a href="{{ route('students.show', $student, false) }}" class="btn btn-primary btn-compact">Открыть</a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>

    <section class="panel" style="margin-top: 22px;">
        <div class="page-header" style="margin-bottom: 12px;">
            <div>
                <h2>Преподаватели без предметов</h2>
                <p class="page-subtitle">Назначьте преподавателю предметы, чтобы он мог создавать уроки и вести журнал по группам.</p>
            </div>
        </div>

        @if($teachersWithoutSubjects->isEmpty())
            <div class="empty-state">У всех преподавателей есть назначенные предметы.</div>
        @else
            <div class="table-wrap">
                <table class="responsive-table">
                    <thead>
                        <tr>
                            <th>Учитель</th>
                            <th>Email</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($teachersWithoutSubjects as $teacher)
                            <tr>
                                <td data-label="Учитель"><strong>{{ $teacher->name }}</strong></td>
                                <td data-label="Email">{{ $teacher->email }}</td>
                                <td data-label="Действия">
                                    <div class="actions">
                                        <a href="{{ route('admin.teachers.show', $teacher, false) }}" class="btn btn-primary btn-compact">Открыть</a>
                                        <a href="{{ route('admin.teachers.edit', $teacher, false) }}" class="btn btn-secondary btn-compact">Изменить</a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
</div>
@endsection
