@extends('layouts.app')

@section('content')
<div class="container narrow">
    <div class="page-header">
        <div>
            <h1 class="page-title">Изменить учителя</h1>
            <p class="page-subtitle">Обновите данные для входа или задайте новый пароль преподавателю.</p>
        </div>

        <a href="{{ route('admin.teachers.show', $teacher, false) }}" class="btn btn-secondary">К карточке</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <section class="form-card">
        <h2>Данные преподавателя</h2>

        <form action="{{ route('admin.teachers.update', $teacher) }}" method="POST" class="form-grid">
            @csrf
            @method('PATCH')

            <div class="field">
                <label for="name">Имя</label>
                <input id="name" type="text" name="name" value="{{ old('name', $teacher->name) }}" required autocomplete="name">
                @error('name') <div class="error-message">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email', $teacher->email) }}" required autocomplete="email">
                @error('email') <div class="error-message">{{ $message }}</div> @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Сохранить</button>
                <a href="{{ route('admin.teachers.index', [], false) }}" class="btn btn-secondary">К списку</a>
            </div>
        </form>
    </section>

    <section class="form-card">
        <h2>Сброс пароля</h2>
        <p class="page-subtitle">Новый пароль сразу заменит старый. Передайте его преподавателю лично.</p>

        @if($teacher->login_password)
            <div class="credential-row" style="margin-bottom: 16px;">
                <span>Текущий выданный пароль</span>
                <div class="password-line">
                    <input id="current_teacher_password" type="password" value="{{ $teacher->login_password }}" readonly>
                    <button type="button" class="btn btn-secondary btn-compact" data-toggle-password="#current_teacher_password">Показать</button>
                </div>
            </div>
        @else
            <div class="alert">
                Текущий пароль был создан до появления защищённого хранения и не может быть показан. Укажите новый пароль ниже.
            </div>
        @endif

        <form action="{{ route('admin.teachers.password', $teacher) }}" method="POST">
            @csrf
            @method('PATCH')

            <div class="form-grid">
                <div class="field">
                    <label for="password">Новый пароль</label>
                    <input id="password" type="password" name="password" required autocomplete="new-password">
                    @error('password') <div class="error-message">{{ $message }}</div> @enderror
                </div>

                <div class="field">
                    <label for="password_confirmation">Повторите пароль</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password">
                </div>
            </div>

            <label class="checkbox-line" for="show_password">
                <input id="show_password" type="checkbox" data-show-password="#password, #password_confirmation">
                <span>Показать пароль</span>
            </label>

            <div class="form-actions form-actions-start">
                <button type="submit" class="btn btn-primary">Обновить пароль</button>
            </div>
        </form>
    </section>
</div>

<script>
    document.querySelectorAll('[data-show-password]').forEach((toggle) => {
        const targets = toggle.dataset.showPassword.split(',').map((selector) => selector.trim());

        toggle.addEventListener('change', () => {
            targets.forEach((selector) => {
                const field = document.querySelector(selector);

                if (field) {
                    field.type = toggle.checked ? 'text' : 'password';
                }
            });
        });
    });

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
