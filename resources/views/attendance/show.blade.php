@extends('layouts.app')

@section('content')
<div class="container">
    <div class="page-header">
        <div>
            @if($selectedSubject)
                <span class="badge">{{ $group->name }}</span>
                <h1 class="page-title" style="margin-top: 10px;">{{ $selectedSubject->name }}</h1>
                <p class="page-subtitle">Создавайте уроки внутри предмета и быстро находите нужные занятия по дате или теме.</p>
            @else
                <h1 class="page-title">Журнал группы {{ $group->name }}</h1>
                <p class="page-subtitle">Выберите предмет, чтобы открыть его уроки. Общий список не перегружает страницу.</p>
            @endif
        </div>

        <div class="actions">
            @if($selectedSubject)
                <a href="{{ route('groups.attendance', ['group' => $group->id]) }}" class="btn btn-secondary">К предметам</a>
            @else
                <a href="{{ route('groups.show', $group) }}" class="btn btn-secondary">Карточка группы</a>
                <a href="{{ route('groups.index') }}" class="btn btn-secondary">К группам</a>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    @if($selectedSubject)
        <div class="panel teacher-workspace">
            <div class="workspace-head">
                <div>
                    <h2>Новый урок</h2>
                    <p class="page-subtitle">Урок будет создан сразу в предмете «{{ $selectedSubject->name }}».</p>
                </div>
            </div>

            <form action="{{ route('attendance.lessons.store') }}" method="POST" class="stack">
                @csrf
                <input type="hidden" name="group_id" value="{{ $group->id }}">
                <input type="hidden" name="subject_id" value="{{ $selectedSubject->id }}">
                <input type="hidden" name="return_to" value="{{ request()->fullUrl() }}">

                <div class="form-grid lesson-form-grid">
                    <div class="field">
                        <label for="date">Дата</label>
                        <input id="date" type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required>
                    </div>

                    <div class="field field-grow">
                        <label for="topic">Тема</label>
                        <input id="topic" type="text" name="topic" value="{{ old('topic') }}" placeholder="Например: Практическая работа">
                    </div>

                    <div class="field field-grow">
                        <label for="description">Описание</label>
                        <input id="description" type="text" name="description" value="{{ old('description') }}" placeholder="Короткое пояснение к уроку">
                    </div>
                </div>

                <div class="actions">
                    <button type="submit" class="btn btn-success">Создать и отметить</button>
                </div>
            </form>
        </div>
    @endif

    <div class="panel teacher-workspace" style="margin-top: {{ $selectedSubject ? '20px' : '20px' }};">
        @unless($selectedSubject)
            <div class="workspace-head">
                <div>
                    <h2>Предметы</h2>
                    <p class="page-subtitle">Нажмите на предмет, чтобы открыть его уроки и создать новое занятие.</p>
                </div>
            </div>
        @endunless

        <form action="{{ route('groups.attendance', $group) }}" method="GET" class="filter-panel">
            @if($selectedSubject)
                <input type="hidden" name="subject_id" value="{{ $selectedSubject->id }}">
            @else
                <div class="field">
                    <label for="filter_subject">Предмет</label>
                    <select id="filter_subject" name="subject_id">
                        <option value="">Все предметы</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" @selected((string) $filters['subject_id'] === (string) $subject->id)>{{ $subject->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="field">
                <label for="date_from">С даты</label>
                <input id="date_from" type="date" name="date_from" value="{{ $filters['date_from'] }}">
            </div>

            <div class="field">
                <label for="date_to">По дату</label>
                <input id="date_to" type="date" name="date_to" value="{{ $filters['date_to'] }}">
            </div>

            <div class="field">
                <label for="status">Заполненность</label>
                <select id="status" name="status">
                    <option value="">Все уроки</option>
                    <option value="filled" @selected($filters['status'] === 'filled')>Заполненные</option>
                    <option value="has_absent" @selected($filters['status'] === 'has_absent')>Есть пропуски</option>
                </select>
            </div>

            <div class="field">
                <label for="sort">Сортировка</label>
                <select id="sort" name="sort">
                    <option value="date_desc" @selected($filters['sort'] === 'date_desc')>Новые сверху</option>
                    <option value="date_asc" @selected($filters['sort'] === 'date_asc')>Старые сверху</option>
                    @unless($selectedSubject)
                        <option value="subject" @selected($filters['sort'] === 'subject')>По предметам</option>
                    @endunless
                    <option value="progress" @selected($filters['sort'] === 'progress')>По заполненности</option>
                </select>
            </div>

            <div class="field field-grow">
                <label for="q">Поиск</label>
                <input id="q" type="search" name="q" value="{{ $filters['q'] }}" placeholder="Тема урока">
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">Найти</button>
                <a href="{{ $selectedSubject ? route('groups.attendance', ['group' => $group->id, 'subject_id' => $selectedSubject->id]) : route('groups.attendance', $group) }}" class="btn btn-secondary">Сбросить</a>
            </div>
        </form>

        @if(!$selectedSubject)
            @if($subjectSummaries->count() > 0)
                <div class="subject-lesson-list">
                    @foreach($subjectSummaries as $item)
                        @php
                            $subject = $item['subject'];
                            $query = array_filter(array_merge(request()->query(), [
                                'subject_id' => $subject->id,
                                'sort' => 'date_desc',
                            ]), fn ($value) => $value !== null && $value !== '');
                        @endphp

                        <a href="{{ route('groups.attendance', array_merge(['group' => $group->id], $query)) }}" class="subject-toggle subject-link-button">
                            <div>
                                <h3>{{ $subject->name }}</h3>
                                <p>{{ $item['lessons'] }} уроков</p>
                            </div>
                            <span class="subject-toggle-meta">
                                <span class="subject-toggle-icon" aria-hidden="true">→</span>
                            </span>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    В этой группе пока нет предметов или уроков.
                    <div style="margin-top: 14px;">
                        <a href="{{ route('subjects.create') }}" class="btn btn-success">Добавить предмет</a>
                    </div>
                </div>
            @endif
        @elseif($lessons->count() > 0)
            <div class="lesson-card-list subject-lessons">
                @foreach($lessons as $lesson)
                    @php
                        $markedCount = $lesson->attendances_count;
                        $isFilled = $studentsCount > 0 && $markedCount >= $studentsCount;
                    @endphp

                    <article class="lesson-card">
                        <div class="lesson-meta">
                            <span>{{ \Illuminate\Support\Carbon::parse($lesson->date)->format('d.m.Y') }}</span>
                            <span class="badge {{ $isFilled ? 'badge-success' : 'badge-warning-soft' }}">{{ $isFilled ? 'Заполнено' : 'В работе' }}</span>
                        </div>

                        <h4>{{ $lesson->topic ?: 'Без темы' }}</h4>

                        @if($lesson->description)
                            <p>{{ $lesson->description }}</p>
                        @endif

                        <div class="lesson-actions">
                            <a href="{{ route('attendance.lesson.mark', ['lesson' => $lesson->id, 'return_to' => request()->fullUrl()]) }}" class="btn btn-primary">{{ $isFilled ? 'Открыть' : 'Заполнить' }}</a>

                            <form action="{{ route('attendance.lesson.destroy', $lesson) }}" method="POST" onsubmit="return confirm('Удалить этот урок?')">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="return_to" value="{{ request()->fullUrl() }}">
                                <button type="submit" class="btn btn-danger">Удалить</button>
                            </form>
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <div class="empty-state">По выбранным условиям уроки не найдены. Создайте первый урок по этому предмету выше.</div>
        @endif
    </div>
</div>
@endsection
