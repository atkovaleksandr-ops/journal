@extends('layouts.app')

@section('content')
<div class="container">
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ $teacher->name }}</h1>
            <p class="page-subtitle">{{ $teacher->email }} · преподаватель создан {{ $teacher->created_at->format('d.m.Y') }}</p>
        </div>

        <div class="section-actions">
            <a href="{{ route('admin.teachers.edit', $teacher, false) }}" class="btn btn-primary">Изменить</a>
            <a href="{{ route('admin.teachers.index', [], false) }}" class="btn btn-secondary">К списку</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="stat-grid">
        <div class="stat-card">
            <span class="stat-value">{{ $teacher->subjects_count }}</span>
            <span class="stat-label">Предметы</span>
        </div>
        <div class="stat-card">
            <span class="stat-value">{{ $teacher->lessons_count }}</span>
            <span class="stat-label">Уроки</span>
        </div>
        <div class="stat-card">
            <span class="stat-value">{{ $subjects->pluck('group_id')->filter()->unique()->count() }}</span>
            <span class="stat-label">Группы</span>
        </div>
    </div>

    <section class="panel" style="margin-bottom: 18px;">
        <h2>Данные для входа</h2>
        <p class="page-subtitle">Пароль хранится в зашифрованном виде и доступен только администратору.</p>

        <div class="credential-grid">
            <div class="credential-row">
                <span>Email</span>
                <strong>{{ $teacher->email }}</strong>
            </div>
            <div class="credential-row">
                <span>Выданный пароль</span>
                @if($teacher->login_password)
                    <div class="password-line">
                        <input id="teacherPassword" type="password" value="{{ $teacher->login_password }}" readonly>
                        <button type="button" class="btn btn-secondary btn-compact" data-toggle-password="#teacherPassword">Показать</button>
                    </div>
                @else
                    <div>
                        <strong>Не сохранён</strong>
                        <p class="muted">Старый хеш нельзя расшифровать. Задайте новый пароль в разделе «Изменить».</p>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <div class="dashboard-grid">
        <section class="panel">
            <h2>Предметы преподавателя</h2>
            <p class="page-subtitle">Быстрый обзор нагрузки по группам.</p>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Предмет</th>
                            <th>Группа</th>
                            <th>Уроки</th>
                            <th>Совместное ведение</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subjects as $subject)
                            <tr>
                                <td><strong>{{ $subject->name }}</strong></td>
                                <td>{{ $subject->group->name ?? 'Без группы' }}</td>
                                <td>{{ $subject->lessons_count }}</td>
                                <td>
                                    @if($subject->coTeachers->isNotEmpty())
                                        {{ $subject->coTeachers->pluck('name')->join(', ') }}
                                    @else
                                        <span class="muted">Только этот преподаватель</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4">У преподавателя пока нет предметов.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="panel">
            <h2>Последние уроки</h2>
            <p class="page-subtitle">Помогает быстро понять, ведется ли журнал.</p>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Дата</th>
                            <th>Предмет</th>
                            <th>Группа</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentLessons as $lesson)
                            <tr>
                                <td>{{ \Illuminate\Support\Carbon::parse($lesson->date)->format('d.m.Y') }}</td>
                                <td><strong>{{ $lesson->subject->name ?? 'Предмет удален' }}</strong></td>
                                <td>{{ $lesson->group->name ?? 'Группа удалена' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3">Уроки пока не созданы.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>

<script>
    document.querySelectorAll('[data-toggle-password]').forEach((button) => {
        button.addEventListener('click', () => {
            const field = document.querySelector(button.dataset.togglePassword);

            if (!field) {
                return;
            }

            const visible = field.type === 'text';
            field.type = visible ? 'password' : 'text';
            button.textContent = visible ? 'Показать' : 'Скрыть';
        });
    });
</script>
@endsection
