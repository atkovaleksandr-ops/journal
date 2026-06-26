@extends('layouts.app')

@section('content')
<div class="container">
    <div class="page-header">
        <div>
            <h1 class="page-title">Личный кабинет администратора</h1>
            <p class="page-subtitle">Общий обзор пользователей, учебных данных и последних созданных аккаунтов.</p>
        </div>
    </div>

    <div class="stat-grid">
        <div class="stat-card">
            <span class="stat-value">{{ $teachersCount ?? 0 }}</span>
            <span class="stat-label">Учителя</span>
        </div>
        <div class="stat-card">
            <span class="stat-value">{{ $studentsCount ?? 0 }}</span>
            <span class="stat-label">Студенты</span>
        </div>
        <div class="stat-card">
            <span class="stat-value">{{ $groupsCount ?? 0 }}</span>
            <span class="stat-label">Группы</span>
        </div>
        <div class="stat-card">
            <span class="stat-value">{{ $subjectsCount ?? 0 }}</span>
            <span class="stat-label">Предметы</span>
        </div>
        <div class="stat-card">
            <span class="stat-value">{{ $lessonsCount ?? 0 }}</span>
            <span class="stat-label">Уроки</span>
        </div>
        <div class="stat-card">
            <span class="stat-value">{{ $attendancesCount ?? 0 }}</span>
            <span class="stat-label">Отметки посещения</span>
        </div>
    </div>

    <div class="dashboard-grid">
        <a href="{{ route('admin.teachers.index', [], false) }}" class="dashboard-card">
            <h3>Учителя</h3>
            <p>Создание, просмотр нагрузки, изменение данных и сброс пароля преподавателей.</p>
            <span class="card-link">Перейти к учителям</span>
        </a>

        <a href="{{ route('students.index', [], false) }}" class="dashboard-card">
            <h3>Студенты</h3>
            <p>Найдите студента по группе, номеру или email, откройте карточку, измените данные для входа или удалите аккаунт.</p>
            <span class="card-link">Перейти к студентам</span>
        </a>

        <a href="{{ route('admin.security', [], false) }}" class="dashboard-card">
            <h3>Доступы</h3>
            <p>Проверьте, у кого есть данные для входа, а каким преподавателям нужно назначить предметы.</p>
            <span class="card-link">{{ $studentsWithoutLoginCount ?? 0 }} студентов без готового входа</span>
        </a>
    </div>

    <section class="panel account-history">
        <div class="section-head">
            <div>
                <h2>История создания аккаунтов</h2>
                <p class="page-subtitle">Последние добавленные учителя и студенты, чтобы быстро проверить новые записи.</p>
            </div>
        </div>

        <div class="dashboard-grid">
            <div>
                <div class="account-history-title">
                    <h3>Последние учителя</h3>
                </div>
                <div class="table-wrap table-wrap-fit">
                    <table>
                        <tbody>
                            @forelse($latestTeachers as $teacher)
                                <tr>
                                    <td>
                                        <strong>{{ $teacher->name }}</strong><br>
                                        <span class="muted">{{ $teacher->email }}</span>
                                    </td>
                                    <td class="text-right">
                                        <a href="{{ route('admin.teachers.show', $teacher, false) }}" class="btn btn-secondary btn-compact">Открыть</a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td>Учителя пока не добавлены.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div>
                <div class="account-history-title">
                    <h3>Последние студенты</h3>
                </div>
                <div class="table-wrap table-wrap-fit">
                    <table>
                        <tbody>
                            @forelse($latestStudents as $student)
                                <tr>
                                    <td>
                                        <strong>{{ $student->last_name }} {{ $student->first_name }}</strong><br>
                                        <span class="muted">{{ $student->group->name ?? 'Без группы' }} · {{ $student->email ?: 'email не указан' }}</span>
                                    </td>
                                    <td class="text-right">
                                        <a href="{{ route('students.show', $student, false) }}" class="btn btn-secondary btn-compact">Открыть</a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td>Студенты пока не добавлены.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
