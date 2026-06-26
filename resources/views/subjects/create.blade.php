@extends('layouts.app')

@section('content')
<div class="container">
    <div class="page-header">
        <div>
            <h1 class="page-title">Добавить предмет</h1>
            <p class="page-subtitle">Предмет закрепляется за вами и выбранной группой.</p>
        </div>
    </div>

    <form action="{{ route('subjects.store') }}" method="POST" class="stack">
        @csrf

        <div class="form-grid">
            <div class="field">
                <label for="name">Название предмета</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" placeholder="Например: Математика" required>
                @error('name') <div class="error-message">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="group_id">Группа</label>
                <select id="group_id" name="group_id" required>
                    <option value="">Выберите группу</option>
                    @foreach($groups as $group)
                        <option value="{{ $group->id }}" @selected(old('group_id') == $group->id)>{{ $group->name }}</option>
                    @endforeach
                </select>
                @error('group_id') <div class="error-message">{{ $message }}</div> @enderror
            </div>

            <div class="field field-full">
                <label for="description">Описание</label>
                <textarea id="description" name="description" rows="4" placeholder="Кратко: темы, формат занятий или заметки">{{ old('description') }}</textarea>
                @error('description') <div class="error-message">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="actions">
            <button type="submit" class="btn btn-success">Сохранить</button>
            <a href="{{ route('subjects.index') }}" class="btn btn-secondary">Назад</a>
        </div>
    </form>

    <div id="assignmentNotice" class="alert" hidden></div>
</div>

@php
    $assignmentPayload = $existingAssignments->map(fn ($subject) => [
        'name' => mb_strtolower(trim((string) preg_replace('/\s+/u', ' ', trim($subject->name)))),
        'group_id' => (string) $subject->group_id,
        'teacher_id' => (string) $subject->teacher_id,
        'teacher' => $subject->teacher?->name,
    ])->values();
@endphp

<script>
    const assignments = @json($assignmentPayload);
    const currentTeacherId = @json((string) auth()->id());
    const nameField = document.querySelector('#name');
    const groupField = document.querySelector('#group_id');
    const notice = document.querySelector('#assignmentNotice');
    const submitButton = document.querySelector('button[type="submit"]');

    function normalizeSubjectName(value) {
        return value.trim().replace(/\s+/gu, ' ').toLocaleLowerCase('ru');
    }

    function updateAssignmentNotice() {
        const name = normalizeSubjectName(nameField.value);
        const groupId = groupField.value;
        const matches = assignments.filter((item) => item.name === name && item.group_id === groupId);

        if (!name || !groupId || matches.length === 0) {
            notice.hidden = true;
            submitButton.disabled = false;
            return;
        }

        const ownAssignment = matches.some((item) => item.teacher_id === currentTeacherId);

        if (ownAssignment) {
            notice.className = 'alert alert-danger';
            notice.textContent = 'У вас уже есть этот предмет в выбранной группе. Измените название или выберите другую группу.';
            notice.hidden = false;
            submitButton.disabled = true;
            return;
        }

        const teachers = [...new Set(matches.map((item) => item.teacher).filter(Boolean))];
        notice.className = 'alert';
        notice.textContent = `Этот предмет в выбранной группе уже ведут: ${teachers.join(', ')}. Вы можете добавить своё назначение; журналы останутся раздельными.`;
        notice.hidden = false;
        submitButton.disabled = false;
    }

    nameField.addEventListener('input', updateAssignmentNotice);
    groupField.addEventListener('change', updateAssignmentNotice);
    updateAssignmentNotice();
</script>
@endsection
