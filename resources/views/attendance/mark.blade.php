@extends('layouts.app')

@section('content')
<div class="container">
    @php
        $returnTo = request('return_to', route('groups.attendance', ['group' => $lesson->group_id, 'subject_id' => $lesson->subject_id]));
    @endphp

    <div class="page-header">
        <div>
            <h1 class="page-title">Посещаемость на уроке</h1>
            <p class="page-subtitle">
                {{ $lesson->group->name }} · {{ $lesson->subject->name }} · {{ \Illuminate\Support\Carbon::parse($lesson->date)->format('d.m.Y') }}
                @if($lesson->topic) · {{ $lesson->topic }} @endif
            </p>
        </div>
        <a href="{{ $returnTo }}" class="btn btn-secondary">Назад к урокам</a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">@foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>
    @endif

    <form action="{{ route('attendance.lesson.save', $lesson) }}" method="POST" class="stack">
        @csrf
        <input type="hidden" name="return_to" value="{{ $returnTo }}">

        @if($lesson->group->students->count() > 0)
            <div class="filter-panel lesson-mark-tools">
                <div class="field field-grow">
                    <label for="studentSearch">Найти студента</label>
                    <input id="studentSearch" type="search" placeholder="ФИО или номер">
                </div>
                <div class="filter-actions">
                    <button type="button" class="btn btn-secondary" data-set-attendance="present">Все присутствовали</button>
                    <button type="button" class="btn btn-secondary" data-set-attendance="absent">Все отсутствовали</button>
                </div>
            </div>

            <table class="responsive-table lesson-mark-table">
                <thead><tr><th>Студент</th><th>Статус посещения</th><th>Заметка</th></tr></thead>
                <tbody>
                    @foreach($lesson->group->students->sortBy([['last_name', 'asc'], ['first_name', 'asc']]) as $student)
                        @php
                            $savedStatus = $attendances->get($student->id)?->status;
                            $currentStatus = in_array($savedStatus, ['present', 'absent'], true) ? $savedStatus : 'present';
                        @endphp
                        <tr data-student-row data-search="{{ mb_strtolower($student->last_name . ' ' . $student->first_name . ' ' . $student->student_number . ' ' . $student->email) }}">
                            <td data-label="Студент">
                                <strong>{{ $student->last_name }} {{ $student->first_name }}</strong>
                                <div class="muted">{{ $student->student_number ?: 'Номер не указан' }}</div>
                            </td>
                            <td data-label="Статус посещения">
                                <select name="attendance[{{ $student->id }}][status]" data-attendance-select required>
                                    <option value="present" @selected($currentStatus === 'present')>Присутствовал</option>
                                    <option value="absent" @selected($currentStatus === 'absent')>Отсутствовал</option>
                                </select>
                            </td>
                            <td data-label="Заметка">
                                <input
                                    type="text"
                                    name="attendance[{{ $student->id }}][note]"
                                    value="{{ old("attendance.{$student->id}.note", $attendances->get($student->id)?->note) }}"
                                    maxlength="500"
                                    placeholder="Причина, комментарий"
                                    class="note-input"
                                >
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="actions sticky-actions">
                <button type="submit" class="btn btn-success">Сохранить посещаемость</button>
                <a href="{{ $returnTo }}" class="btn btn-secondary">Отмена</a>
            </div>
        @else
            <div class="empty-state">В группе пока нет студентов. Добавьте студентов, чтобы отмечать посещаемость.</div>
        @endif
    </form>
</div>

<script>
    const searchInput = document.querySelector('#studentSearch');
    searchInput?.addEventListener('input', () => {
        const query = searchInput.value.trim().toLowerCase();
        document.querySelectorAll('[data-student-row]').forEach((row) => {
            row.hidden = query !== '' && !row.dataset.search.includes(query);
        });
    });

    document.querySelectorAll('[data-set-attendance]').forEach((button) => {
        button.addEventListener('click', () => {
            document.querySelectorAll('[data-student-row]:not([hidden]) [data-attendance-select]').forEach((select) => {
                select.value = button.dataset.setAttendance;
            });
        });
    });
</script>
@endsection
