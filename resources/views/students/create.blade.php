@extends('layouts.app')

@section('content')
<div class="container">
    <div class="page-header">
        <div>
            <h1 class="page-title">Добавить студента</h1>
            <p class="page-subtitle">Заполните данные студента. Если указан email, система сразу подготовит личный кабинет.</p>
        </div>
    </div>

    <form action="{{ route('students.store') }}" method="POST" class="stack">
        @csrf
        @if($pendingUser)
            <input type="hidden" name="user_id" value="{{ $pendingUser->id }}">
        @endif

        <div class="form-grid">
            <div class="field">
                <label for="first_name">Имя</label>
                <input id="first_name" type="text" name="first_name" value="{{ old('first_name') }}" required>
                @error('first_name') <div class="error-message">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="last_name">Фамилия</label>
                <input id="last_name" type="text" name="last_name" value="{{ old('last_name') }}" required>
                @error('last_name') <div class="error-message">{{ $message }}</div> @enderror
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

            <div class="field">
                <label for="student_number">Номер студента</label>
                <input id="student_number" type="text" name="student_number" value="{{ old('student_number') }}" placeholder="Например: S-001">
                @error('student_number') <div class="error-message">{{ $message }}</div> @enderror
            </div>

            <div class="field field-full">
                <label for="email">Email для входа</label>
                <input id="email" type="email" name="email" value="{{ old('email', $pendingUser?->email) }}" placeholder="student@example.com" @readonly($pendingUser)>
                @if($pendingUser)
                    <p class="field-hint">Email взят из зарегистрированного аккаунта и останется привязанным к карточке студента.</p>
                @endif
                @error('email') <div class="error-message">{{ $message }}</div> @enderror
            </div>

            <div class="field field-full">
                <label for="login_password">Пароль для входа</label>
                <div class="password-line">
                    <input id="login_password" type="password" name="login_password" value="{{ old('login_password') }}" placeholder="Если оставить пустым: student123" autocomplete="new-password">
                    <button type="button" class="btn btn-secondary btn-compact" data-toggle-password="#login_password">Показать</button>
                </div>
                <p class="field-hint">
                    @if($pendingUser)
                        Оставьте поле пустым, чтобы не менять пароль уже зарегистрированного аккаунта.
                    @else
                        Этот пароль можно выдать студенту вместе с email.
                    @endif
                </p>
                @error('login_password') <div class="error-message">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="actions">
            <button type="submit" class="btn btn-success">Сохранить</button>
            <a href="{{ route('students.index') }}" class="btn btn-secondary">Назад</a>
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
