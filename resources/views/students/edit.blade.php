@extends('layouts.app')

@section('content')
<div class="container">
    <div class="page-header">
        <div>
            <h1 class="page-title">Редактировать студента</h1>
            <p class="page-subtitle">{{ $student->first_name }} {{ $student->last_name }}</p>
        </div>
    </div>

    <form action="{{ route('students.update', $student) }}" method="POST" class="stack">
        @csrf
        @method('PATCH')
        <input type="hidden" name="return_to" value="{{ request('return_to', route('students.show', $student, false)) }}">

        <div class="form-grid">
            <div class="field">
                <label for="first_name">Имя</label>
                <input id="first_name" type="text" name="first_name" value="{{ old('first_name', $student->first_name) }}" required>
                @error('first_name') <div class="error-message">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="last_name">Фамилия</label>
                <input id="last_name" type="text" name="last_name" value="{{ old('last_name', $student->last_name) }}" required>
                @error('last_name') <div class="error-message">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="group_id">Группа</label>
                <select id="group_id" name="group_id" required>
                    @foreach($groups as $group)
                        <option value="{{ $group->id }}" @selected(old('group_id', $student->group_id) == $group->id)>{{ $group->name }}</option>
                    @endforeach
                </select>
                @error('group_id') <div class="error-message">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="student_number">Номер студента</label>
                <input id="student_number" type="text" name="student_number" value="{{ old('student_number', $student->student_number) }}">
                @error('student_number') <div class="error-message">{{ $message }}</div> @enderror
            </div>

            <div class="field field-full">
                <label for="email">Email для входа</label>
                <input id="email" type="email" name="email" value="{{ old('email', $student->email) }}">
                @error('email') <div class="error-message">{{ $message }}</div> @enderror
            </div>

            <div class="field field-full">
                <label for="current_login_password">Текущий пароль для входа</label>
                <div class="password-line">
                    <input id="current_login_password" type="password" value="{{ $studentPassword ?: 'Пароль не задан' }}" readonly>
                    <button type="button" class="btn btn-secondary btn-compact" data-toggle-password="#current_login_password">Показать</button>
                </div>
            </div>

            <div class="field field-full">
                <label for="login_password">Новый пароль для входа</label>
                <div class="password-line">
                    <input id="login_password" type="password" name="login_password" value="{{ old('login_password') }}" placeholder="Оставьте пустым, если пароль менять не нужно" autocomplete="new-password">
                    <button type="button" class="btn btn-secondary btn-compact" data-toggle-password="#login_password">Показать</button>
                </div>
                <p class="field-hint">Если указать новый пароль, он обновится и у аккаунта студента.</p>
                @error('login_password') <div class="error-message">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="actions">
            <button type="submit" class="btn btn-success">Сохранить изменения</button>
            <a href="{{ request('return_to', route('students.show', $student, false)) }}" class="btn btn-secondary">Назад</a>
        </div>
    </form>
</div>

<script>
    document.querySelectorAll('[data-toggle-password]').forEach((button) => {
        button.addEventListener('click', () => {
            const input = document.querySelector(button.dataset.togglePassword);

            if (!input) {
                return;
            }

            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';
            button.textContent = isHidden ? 'Скрыть' : 'Показать';
        });
    });
</script>
@endsection
