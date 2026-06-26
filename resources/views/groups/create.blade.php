@extends('layouts.app')

@section('content')
<div class="container">
    <div class="page-header">
        <div>
            <h1 class="page-title">Создать группу</h1>
            <p class="page-subtitle">Группа объединяет студентов и предметы, по ней открывается журнал уроков.</p>
        </div>
    </div>

    <form action="{{ route('groups.store') }}" method="POST" class="stack group-form">
        @csrf

        <div class="group-builder">
            <div class="group-builder-fields">
                <div class="field">
                    <label for="program_name">Направление</label>
                    <input id="program_name" type="text" name="program_name" value="{{ old('program_name') }}" list="program_suggestions" placeholder="Вычислительные технологии" required>
                    @error('program_name') <div class="error-message">{{ $message }}</div> @enderror
                </div>

                <div class="group-builder-row">
                    <div class="field">
                        <label for="course">Курс</label>
                        <select id="course" name="course" required>
                            @for($course = 1; $course <= 6; $course++)
                                <option value="{{ $course }}" @selected((int) old('course', 1) === $course)>{{ $course }} курс</option>
                            @endfor
                        </select>
                        @error('course') <div class="error-message">{{ $message }}</div> @enderror
                    </div>

                    <div class="field">
                        <label for="group_number">Группа</label>
                        <select id="group_number" name="group_number" required>
                            @for($number = 1; $number <= 9; $number++)
                                <option value="{{ $number }}" @selected((int) old('group_number', 1) === $number)>{{ $number }} группа</option>
                            @endfor
                        </select>
                        @error('group_number') <div class="error-message">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            <aside class="group-code-card" aria-live="polite">
                <span class="group-code-kicker">Код группы</span>
                <strong id="short_name_preview" class="group-code-value">—</strong>
                <p id="full_name_preview" class="group-code-text">Выберите направление, курс и группу.</p>
            </aside>
        </div>

        <datalist id="program_suggestions">
            <option value="Вычислительные технологии"></option>
            <option value="Программное обеспечение"></option>
            <option value="Информационные системы"></option>
            <option value="Дизайн интерфейсов"></option>
        </datalist>

        <div class="actions">
            <button type="submit" class="btn btn-success">Сохранить</button>
            <a href="{{ route('groups.index') }}" class="btn btn-secondary">Назад</a>
        </div>
    </form>
</div>

<script>
    const programField = document.querySelector('#program_name');
    const courseField = document.querySelector('#course');
    const groupNumberField = document.querySelector('#group_number');
    const shortNamePreview = document.querySelector('#short_name_preview');
    const fullNamePreview = document.querySelector('#full_name_preview');

    function normalizeWords(value) {
        return value.trim().replace(/\s+/gu, ' ').split(/[\s-]+/u).filter(Boolean);
    }

    function updateShortNamePreview() {
        const initials = normalizeWords(programField.value)
            .map((word) => word.slice(0, 1).toLocaleUpperCase('ru'))
            .join('');
        const course = courseField.value || '1';
        const groupNumber = groupNumberField.value || '1';

        shortNamePreview.textContent = initials ? `${initials}-${course}${groupNumber}` : '—';
        fullNamePreview.textContent = initials
            ? `${programField.value.trim().replace(/\s+/gu, ' ')}, ${course} курс, ${groupNumber} группа`
            : 'Выберите направление, курс и группу.';
    }

    [programField, courseField, groupNumberField].forEach((field) => {
        field.addEventListener('input', updateShortNamePreview);
        field.addEventListener('change', updateShortNamePreview);
    });

    updateShortNamePreview();
</script>
@endsection
