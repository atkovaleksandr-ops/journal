@extends('layouts.app')

@section('content')
<div class="container">
    <div class="page-header">
        <div>
            <h1 class="page-title">Студенты</h1>
            <p class="page-subtitle">Управляйте студентами по группам: фильтруйте, ищите по имени, номеру или email.</p>
        </div>

        <a href="{{ route('students.create') }}" class="btn btn-success">Добавить студента</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="group-chip-list">
        <a href="{{ route('students.index') }}" class="group-chip {{ !$filters['group_id'] ? 'is-active' : '' }}">
            Все группы
            <span>{{ $groups->sum('students_count') }}</span>
        </a>

        @foreach($groups as $group)
            <a href="{{ route('students.index', array_filter(array_merge(request()->query(), ['group_id' => $group->id]), fn ($value) => $value !== null && $value !== '')) }}" class="group-chip {{ (string) $filters['group_id'] === (string) $group->id ? 'is-active' : '' }}">
                {{ $group->name }}
                <span>{{ $group->students_count }}</span>
            </a>
        @endforeach
    </div>

    <form action="{{ route('students.index') }}" method="GET" class="filter-panel">
        <div class="field field-grow">
            <label for="q">Поиск</label>
            <input id="q" type="search" name="q" value="{{ $filters['q'] }}" placeholder="ФИО, номер или email">
        </div>

        <div class="field">
            <label for="group_id">Группа</label>
            <select id="group_id" name="group_id">
                <option value="">Все группы</option>
                @foreach($groups as $group)
                    <option value="{{ $group->id }}" @selected((string) $filters['group_id'] === (string) $group->id)>{{ $group->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="field">
            <label for="sort">Сортировка</label>
            <select id="sort" name="sort">
                <option value="name" @selected($filters['sort'] === 'name')>По фамилии</option>
                <option value="group" @selected($filters['sort'] === 'group')>По группе</option>
                <option value="number" @selected($filters['sort'] === 'number')>По номеру</option>
                <option value="activity" @selected($filters['sort'] === 'activity')>По активности</option>
                <option value="newest" @selected($filters['sort'] === 'newest')>Сначала новые</option>
            </select>
        </div>

        <div class="filter-actions">
            <button type="submit" class="btn btn-primary">Показать</button>
            <a href="{{ route('students.index') }}" class="btn btn-secondary">Сбросить</a>
        </div>
    </form>

    @if($students->count() > 0)
        <div class="subject-lesson-list">
            @foreach($studentsByGroup as $groupName => $groupStudents)
                <section class="student-group-section" data-student-group>
                    <button type="button" class="subject-toggle student-group-head" data-student-group-toggle aria-expanded="false">
                        <div>
                            <h3>{{ $groupName }}</h3>
                            <p>{{ $groupStudents->count() }} студентов в текущей подборке</p>
                        </div>
                        <span class="subject-toggle-meta">
                            <span class="badge">{{ $groupStudents->whereNotNull('email')->count() }} с email</span>
                            <span class="subject-toggle-icon student-group-icon" aria-hidden="true">+</span>
                        </span>
                    </button>

                    <div class="student-card-grid student-group-body" hidden>
                        @foreach($groupStudents as $student)
                            <article class="student-card">
                                <div>
                                    <h3>{{ $student->last_name }} {{ $student->first_name }}</h3>
                                    <p>{{ $student->student_number ?: 'Номер не указан' }}</p>
                                </div>

                                <div class="student-card-meta">
                                    <span>{{ $student->email ?: 'email не указан' }}</span>
                                    <span>{{ $student->attendances_count }} отметок посещаемости</span>
                                </div>

                                <div class="lesson-actions">
                                    <a href="{{ route('students.show', ['student' => $student->id, 'return_to' => request()->fullUrl()]) }}" class="btn btn-primary">Открыть</a>
                                    <a href="{{ route('students.edit', ['student' => $student->id, 'return_to' => request()->fullUrl()]) }}" class="btn btn-secondary">Изменить</a>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endforeach
        </div>
    @else
        <div class="empty-state">
            Студенты по выбранным условиям не найдены. Сбросьте фильтр или добавьте нового студента.
        </div>
    @endif
</div>

<script>
    document.querySelectorAll('[data-student-group]').forEach((group) => {
        const toggle = group.querySelector('[data-student-group-toggle]');
        const body = group.querySelector('.student-group-body');
        const icon = group.querySelector('.student-group-icon');

        if (!toggle || !body || !icon) {
            return;
        }

        toggle.addEventListener('click', () => {
            const nextState = toggle.getAttribute('aria-expanded') !== 'true';

            toggle.setAttribute('aria-expanded', String(nextState));
            body.hidden = !nextState;
            icon.textContent = nextState ? '−' : '+';
            group.classList.toggle('is-open', nextState);
        });
    });
</script>
@endsection
