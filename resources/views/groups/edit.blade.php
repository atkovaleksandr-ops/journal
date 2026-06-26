@extends('layouts.app')

@section('content')
<div class="container">
    <div class="page-header">
        <div>
            <h1 class="page-title">Редактировать группу</h1>
            <p class="page-subtitle">{{ $group->name }}</p>
        </div>
    </div>

    <form action="{{ route('groups.update', $group) }}" method="POST" class="stack">
        @csrf
        @method('PATCH')

        <div class="form-grid">
            <div class="field field-full">
                <label for="program_name">Название направления</label>
                <input id="program_name" type="text" name="program_name" value="{{ old('program_name', $groupIdentity['program_name']) }}" required>
                @error('program_name') <div class="error-message">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="course">Курс</label>
                <input id="course" type="number" name="course" value="{{ old('course', $groupIdentity['course']) }}" min="1" max="6" required>
                @error('course') <div class="error-message">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="group_number">Номер группы</label>
                <input id="group_number" type="number" name="group_number" value="{{ old('group_number', $groupIdentity['group_number']) }}" min="1" max="9" required>
                @error('group_number') <div class="error-message">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="short_name_preview">Короткое название</label>
                <input id="short_name_preview" type="text" value="" readonly>
                <p class="field-hint">Код формируется из первых букв направления, курса и номера группы.</p>
            </div>
        </div>

        <div class="actions">
            <button type="submit" class="btn btn-success">Сохранить изменения</button>
            <a href="{{ route('groups.show', $group) }}" class="btn btn-secondary">Назад</a>
        </div>
    </form>
</div>

<script>
    const programField = document.querySelector('#program_name');
    const courseField = document.querySelector('#course');
    const groupNumberField = document.querySelector('#group_number');
    const shortNamePreview = document.querySelector('#short_name_preview');

    function normalizeWords(value) {
        return value.trim().replace(/\s+/gu, ' ').split(/[\s-]+/u).filter(Boolean);
    }

    function updateShortNamePreview() {
        const initials = normalizeWords(programField.value)
            .map((word) => word.slice(0, 1).toLocaleUpperCase('ru'))
            .join('');
        const course = courseField.value || '1';
        const groupNumber = groupNumberField.value || '1';

        shortNamePreview.value = initials ? `${initials}-${course}${groupNumber}` : '';
    }

    [programField, courseField, groupNumberField].forEach((field) => {
        field.addEventListener('input', updateShortNamePreview);
    });

    updateShortNamePreview();
</script>
@endsection
