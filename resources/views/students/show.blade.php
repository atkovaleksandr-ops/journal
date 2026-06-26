@extends('layouts.app')

@section('content')
@php $studentPassword = $student->login_password ?: 'student123'; @endphp

<div class="container">
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ $student->last_name }} {{ $student->first_name }}</h1>
            <p class="page-subtitle">{{ $student->group->name ?? 'Группа не указана' }} · {{ $student->student_number ?: 'номер не указан' }} · {{ $student->email ?: 'email не указан' }}</p>
        </div>
        <div class="actions">
            <a href="{{ route('students.edit', ['student' => $student->id, 'return_to' => request('return_to', route('students.index', [], false))], false) }}" class="btn btn-warning">Изменить</a>
            <a href="{{ request('return_to', route('students.index', [], false)) }}" class="btn btn-secondary">Назад</a>
            <form action="{{ route('students.destroy', $student) }}" method="POST" onsubmit="return confirm('Удалить студента и его аккаунт для входа?')">
                @csrf @method('DELETE')
                <input type="hidden" name="return_to" value="{{ request('return_to', route('students.index', [], false)) }}">
                <button type="submit" class="btn btn-danger">Удалить</button>
            </form>
        </div>
    </div>

    <div class="stat-grid compact-stats">
        <div class="stat-card"><span class="stat-value">{{ $attendanceSummary['lessons'] }}</span><span class="stat-label">Всего уроков</span></div>
        <div class="stat-card"><span class="stat-value">{{ $attendanceSummary['present'] }}</span><span class="stat-label">Присутствовал</span></div>
        <div class="stat-card"><span class="stat-value">{{ $attendanceSummary['absent'] }}</span><span class="stat-label">Отсутствовал</span></div>
        <div class="stat-card"><span class="stat-value">{{ $attendanceSummary['not_marked'] }}</span><span class="stat-label">Не отмечено</span></div>
        <div class="stat-card"><span class="stat-value">{{ $attendanceSummary['attendance_percent'] !== null ? $attendanceSummary['attendance_percent'].'%' : '—' }}</span><span class="stat-label">Посещаемость</span></div>
    </div>

    <div class="dashboard-grid">
        <section class="dashboard-card">
            <h3>Данные для входа</h3>
            <p>Данные личного кабинета студента.</p>
            <div class="credential-grid">
                <div class="credential-row"><span>Email</span><strong>{{ $student->email ?: 'Не указан' }}</strong></div>
                <div class="credential-row">
                    <span>Пароль</span>
                    <div class="password-line">
                        <input id="studentPassword" type="password" value="{{ $studentPassword }}" readonly>
                        <button type="button" class="btn btn-secondary btn-compact" data-toggle-password="#studentPassword">Показать</button>
                    </div>
                </div>
            </div>
        </section>

        <section class="dashboard-card">
            <h3>Общая посещаемость</h3>
            <p>Сводка по урокам всех предметов группы.</p>
            <div class="mini-stat-row"><span>Отмечено уроков</span><strong>{{ $attendanceSummary['marked'] }}</strong></div>
            <div class="mini-stat-row"><span>Присутствовал</span><strong>{{ $attendanceSummary['present'] }}</strong></div>
            <div class="mini-stat-row"><span>Отсутствовал</span><strong>{{ $attendanceSummary['absent'] }}</strong></div>
            <div class="mini-stat-row"><span>Посещаемость</span><strong>{{ $attendanceSummary['attendance_percent'] !== null ? $attendanceSummary['attendance_percent'].'%' : '—' }}</strong></div>
        </section>
    </div>

    <section class="panel teacher-workspace" style="margin-top: 18px;">
        <div class="workspace-head"><div><h2>Посещаемость по предметам</h2><p class="page-subtitle">Количество уроков, пропусков и незаполненных отметок.</p></div></div>
        @if($subjectSummaries->count())
            <div class="student-card-grid">
                @foreach($subjectSummaries as $item)
                    <article
                        class="student-card subject-attendance-trigger"
                        role="button"
                        tabindex="0"
                        aria-haspopup="dialog"
                        aria-controls="subjectAttendance{{ $item['subject']->id }}"
                        data-open-attendance-dialog="subjectAttendance{{ $item['subject']->id }}"
                    >
                        <div><h3>{{ $item['subject']->name }}</h3><p>{{ $item['subject']->description ?: 'Предмет группы' }}</p></div>
                        <div class="student-card-meta">
                            <span>Всего уроков: <strong>{{ $item['lessons_count'] }}</strong></span>
                            <span>Отмечено: <strong>{{ $item['marked_count'] }}</strong></span>
                        </div>
                        <div class="mini-stat-row"><span>Присутствовал</span><strong>{{ $item['present_count'] }}</strong></div>
                        <div class="mini-stat-row"><span>Отсутствовал</span><strong>{{ $item['absent_count'] }}</strong></div>
                        <div class="mini-stat-row"><span>Не отмечено</span><strong>{{ $item['not_marked_count'] }}</strong></div>
                        <div class="mini-stat-row"><span>Посещаемость</span><strong>{{ $item['attendance_percent'] !== null ? $item['attendance_percent'].'%' : '—' }}</strong></div>
                        <span class="card-link">Показать все даты</span>
                    </article>
                @endforeach
            </div>
        @else
            <div class="empty-state">Для группы пока не добавлены предметы.</div>
        @endif
    </section>

    @foreach($subjectSummaries as $item)
        <dialog id="subjectAttendance{{ $item['subject']->id }}" class="attendance-dialog" aria-labelledby="subjectAttendanceTitle{{ $item['subject']->id }}">
            <div class="attendance-dialog-shell">
                <div class="attendance-dialog-head">
                    <div>
                        <span class="badge">{{ $student->group->name ?? 'Без группы' }}</span>
                        <h2 id="subjectAttendanceTitle{{ $item['subject']->id }}">{{ $item['subject']->name }}</h2>
                        <p>{{ $item['lessons_count'] }} уроков · посещаемость {{ $item['attendance_percent'] !== null ? $item['attendance_percent'].'%' : 'не рассчитана' }}</p>
                    </div>
                    <button type="button" class="dialog-close" aria-label="Закрыть" data-close-attendance-dialog>×</button>
                </div>

                <div class="attendance-dialog-summary">
                    <span><strong>{{ $item['present_count'] }}</strong> присутствовал</span>
                    <span><strong>{{ $item['absent_count'] }}</strong> отсутствовал</span>
                    <span><strong>{{ $item['not_marked_count'] }}</strong> не отмечено</span>
                </div>

                <div class="attendance-dialog-body">
                    @if($item['lesson_history']->count())
                        <table class="responsive-table">
                            <thead><tr><th>Дата</th><th>Тема урока</th><th>Статус</th><th>Заметка</th></tr></thead>
                            <tbody>
                                @foreach($item['lesson_history'] as $historyItem)
                                    @php
                                        $historyStatus = $historyItem['status'];
                                        $historyLabel = match($historyStatus) {
                                            'present' => 'Присутствовал',
                                            'absent' => 'Отсутствовал',
                                            default => 'Не отмечено',
                                        };
                                    @endphp
                                    <tr>
                                        <td data-label="Дата">{{ \Illuminate\Support\Carbon::parse($historyItem['lesson']->date)->format('d.m.Y') }}</td>
                                        <td data-label="Тема">{{ $historyItem['lesson']->topic ?: 'Без темы' }}</td>
                                        <td data-label="Статус">
                                            <span class="badge {{ $historyStatus === 'present' ? 'badge-success' : ($historyStatus === 'absent' ? 'badge-warning-soft' : '') }}">{{ $historyLabel }}</span>
                                        </td>
                                        <td data-label="Заметка">{{ $historyItem['note'] ?: '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="empty-state">По предмету пока нет уроков.</div>
                    @endif
                </div>
            </div>
        </dialog>
    @endforeach

    <section class="student-detail-section" data-student-detail>
        <button type="button" class="subject-toggle student-detail-head" data-student-detail-toggle aria-expanded="false">
            <div><h3>Последние 20 отметок посещаемости</h3><p>Недавние посещения и пропуски по всем предметам</p></div>
            <span class="subject-toggle-meta"><span class="badge">{{ min($student->attendances->count(), 20) }}/{{ $student->attendances->count() }}</span><span class="subject-toggle-icon student-detail-icon" aria-hidden="true">+</span></span>
        </button>
        <div class="panel student-detail-body" hidden>
            @if($student->attendances->count())
                <table class="responsive-table">
                    <thead><tr><th>Дата</th><th>Предмет</th><th>Тема</th><th>Статус</th><th>Заметка</th></tr></thead>
                    <tbody>
                        @foreach($student->attendances->sortByDesc('date')->take(20) as $attendance)
                            @php $isPresent = $attendance->status === 'present'; @endphp
                            <tr>
                                <td data-label="Дата">{{ \Illuminate\Support\Carbon::parse($attendance->date)->format('d.m.Y') }}</td>
                                <td data-label="Предмет">{{ $attendance->subject->name ?? 'Без предмета' }}</td>
                                <td data-label="Тема">{{ $attendance->lesson->topic ?? 'Без темы' }}</td>
                                <td data-label="Статус"><span class="badge {{ $isPresent ? 'badge-success' : 'badge-warning-soft' }}">{{ $isPresent ? 'Присутствовал' : 'Отсутствовал' }}</span></td>
                                <td data-label="Заметка">{{ $attendance->note ?: '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty-state">Отметок посещаемости пока нет.</div>
            @endif
        </div>
    </section>
</div>

<script>
    document.querySelectorAll('[data-student-detail]').forEach((section) => {
        const toggle = section.querySelector('[data-student-detail-toggle]');
        const body = section.querySelector('.student-detail-body');
        const icon = section.querySelector('.student-detail-icon');
        toggle?.addEventListener('click', () => {
            const open = toggle.getAttribute('aria-expanded') !== 'true';
            toggle.setAttribute('aria-expanded', String(open));
            body.hidden = !open;
            icon.textContent = open ? '-' : '+';
            section.classList.toggle('is-open', open);
        });
    });
    document.querySelectorAll('[data-toggle-password]').forEach((button) => {
        button.addEventListener('click', () => {
            const input = document.querySelector(button.dataset.togglePassword);
            if (!input) return;
            const hidden = input.type === 'password';
            input.type = hidden ? 'text' : 'password';
            button.textContent = hidden ? 'Скрыть' : 'Показать';
        });
    });

    const closeAttendanceDialog = (dialog) => {
        if (!dialog) return;
        dialog.close();
        document.body.classList.remove('dialog-open');
    };

    document.querySelectorAll('[data-open-attendance-dialog]').forEach((trigger) => {
        const openDialog = () => {
            const dialog = document.getElementById(trigger.dataset.openAttendanceDialog);
            if (!dialog) return;
            dialog.showModal();
            document.body.classList.add('dialog-open');
        };

        trigger.addEventListener('click', openDialog);
        trigger.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                openDialog();
            }
        });
    });

    document.querySelectorAll('.attendance-dialog').forEach((dialog) => {
        dialog.querySelector('[data-close-attendance-dialog]')?.addEventListener('click', () => closeAttendanceDialog(dialog));
        dialog.addEventListener('click', (event) => {
            if (event.target === dialog) closeAttendanceDialog(dialog);
        });
        dialog.addEventListener('close', () => document.body.classList.remove('dialog-open'));
    });
</script>
@endsection
