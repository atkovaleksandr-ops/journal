@extends('layouts.app')

@section('content')
<div class="container admin-teacher-page">
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

    <section class="panel admin-card">
        <div class="section-head">
            <div>
                <h2>Данные для входа</h2>
                <p class="page-subtitle">Пароль хранится в зашифрованном виде и доступен только администратору.</p>
            </div>
        </div>

        <div class="credential-grid">
            <div class="credential-row admin-credential-row">
                <span>Email</span>
                <strong>{{ $teacher->email }}</strong>
            </div>
            <div class="credential-row admin-credential-row">
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

    <div class="admin-teacher-grid">
        <section class="panel admin-list-panel">
            <div class="section-head">
                <div>
                    <h2>Предметы преподавателя</h2>
                    <p class="page-subtitle">Быстрый обзор нагрузки по группам.</p>
                </div>
            </div>

            <div class="admin-subject-list">
                @forelse($subjects as $subject)
                    <article class="admin-subject-item">
                        <div class="admin-subject-main">
                            <strong>{{ $subject->name }}</strong>
                            @if($subject->coTeachers->isNotEmpty())
                                <span>{{ $subject->coTeachers->pluck('name')->join(', ') }}</span>
                            @else
                                <span>Только этот преподаватель</span>
                            @endif
                        </div>

                        <div class="admin-subject-meta">
                            <span class="badge">{{ $subject->group->name ?? 'Без группы' }}</span>
                            <span class="admin-count-pill">{{ $subject->lessons_count }} уроков</span>
                        </div>
                    </article>
                @empty
                    <div class="empty-state">У преподавателя пока нет предметов.</div>
                @endforelse
            </div>
        </section>

        <section class="panel admin-list-panel">
            <div class="section-head">
                <div>
                    <h2>Последние уроки</h2>
                    <p class="page-subtitle">Помогает быстро понять, ведется ли журнал.</p>
                </div>
            </div>

            <div class="admin-lesson-list">
                @forelse($recentLessons as $lesson)
                    <article class="admin-lesson-item">
                        <time datetime="{{ $lesson->date }}">{{ \Illuminate\Support\Carbon::parse($lesson->date)->format('d.m.Y') }}</time>
                        <div>
                            <strong>{{ $lesson->subject->name ?? 'Предмет удален' }}</strong>
                            <span>{{ $lesson->group->name ?? 'Группа удалена' }}</span>
                        </div>
                    </article>
                @empty
                    <div class="empty-state">Уроки пока не созданы.</div>
                @endforelse
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
