@extends('layouts.app')

@section('content')
<div class="container">
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ $group->name }}</h1>
            <p class="page-subtitle">{{ $group->description ?: 'Описание пока не добавлено.' }}</p>
        </div>

        <div class="actions">
            <a href="{{ route('groups.attendance', $group) }}" class="btn btn-primary">Журнал</a>
            <a href="{{ route('groups.edit', $group) }}" class="btn btn-warning">Редактировать</a>
            <form action="{{ route('groups.destroy', $group) }}" method="POST" onsubmit="return confirm('Удалить группу вместе со студентами, предметами, уроками и посещаемостью?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Удалить</button>
            </form>
            <a href="{{ route('groups.index') }}" class="btn btn-secondary">Все группы</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="stat-grid">
        <div class="stat-card">
            <span class="stat-value">{{ $group->students->count() }}</span>
            <span class="stat-label">Студенты</span>
        </div>
        <div class="stat-card">
            <span class="stat-value">{{ $group->subjects->count() }}</span>
            <span class="stat-label">Предметы</span>
        </div>
    </div>

    <section class="student-detail-section" data-fold-section>
        <button type="button" class="subject-toggle student-detail-head" data-fold-toggle aria-expanded="false">
            <div>
                <h3>Студенты</h3>
                <p>{{ $group->students->count() }} записей в группе</p>
            </div>
            <span class="subject-toggle-meta">
                <span class="badge">{{ $group->students->count() }}</span>
                <span class="subject-toggle-icon student-detail-icon" aria-hidden="true">+</span>
            </span>
        </button>

        <div class="panel student-detail-body" hidden>
            @if($group->students->count())
                <table class="responsive-table">
                    <thead>
                        <tr>
                            <th>Студент</th>
                            <th>Email</th>
                            <th>Номер</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($group->students as $student)
                            <tr>
                                <td data-label="Студент"><strong>{{ $student->last_name }} {{ $student->first_name }}</strong></td>
                                <td data-label="Email">{{ $student->email ?: '—' }}</td>
                                <td data-label="Номер">{{ $student->student_number ?: '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty-state">В этой группе пока нет студентов.</div>
            @endif
        </div>
    </section>

    <section class="student-detail-section" data-fold-section>
        <button type="button" class="subject-toggle student-detail-head" data-fold-toggle aria-expanded="false">
            <div>
                <h3>Предметы</h3>
                <p>{{ $group->subjects->count() }} предметов привязано к группе</p>
            </div>
            <span class="subject-toggle-meta">
                <span class="badge">{{ $group->subjects->count() }}</span>
                <span class="subject-toggle-icon student-detail-icon" aria-hidden="true">+</span>
            </span>
        </button>

        <div class="panel student-detail-body" hidden>
            @if($group->subjects->count())
                <table class="responsive-table">
                    <thead>
                        <tr>
                            <th>Предмет</th>
                            <th>Описание</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($group->subjects as $subject)
                            <tr>
                                <td data-label="Предмет"><strong>{{ $subject->name }}</strong></td>
                                <td data-label="Описание">{{ $subject->description ?: '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty-state">Для этой группы пока нет предметов.</div>
            @endif
        </div>
    </section>
</div>

<script>
    document.querySelectorAll('[data-fold-section]').forEach((section) => {
        const toggle = section.querySelector('[data-fold-toggle]');
        const body = section.querySelector('.student-detail-body');
        const icon = section.querySelector('.student-detail-icon');

        if (!toggle || !body || !icon) {
            return;
        }

        toggle.addEventListener('click', () => {
            const nextState = toggle.getAttribute('aria-expanded') !== 'true';

            toggle.setAttribute('aria-expanded', String(nextState));
            body.hidden = !nextState;
            icon.textContent = nextState ? '−' : '+';
            section.classList.toggle('is-open', nextState);
        });
    });
</script>
@endsection
