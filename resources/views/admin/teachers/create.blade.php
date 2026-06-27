@extends('layouts.app')

@section('content')
<div class="container narrow admin-teacher-edit">
    <div class="page-header">
        <div>
            <h1 class="page-title">Создать учителя</h1>
            <p class="page-subtitle">Заполните данные для входа. Роль преподавателя будет назначена автоматически.</p>
        </div>
    </div>

    <section class="form-card admin-form-card">
        <div class="section-head">
            <div>
                <h2>Данные для входа</h2>
                <p class="page-subtitle">Создайте преподавателя и сразу выдайте ему пароль для первого входа.</p>
            </div>
        </div>

        <form action="{{ route('admin.teachers.store') }}" method="POST" class="admin-form-stack">
            @csrf

            <div class="form-grid admin-two-column">
                <div class="field">
                    <label for="name">Имя</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name">
                    @error('name') <div class="error-message">{{ $message }}</div> @enderror
                </div>

                <div class="field">
                    <label for="email">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email">
                    @error('email') <div class="error-message">{{ $message }}</div> @enderror
                </div>

                <div class="field">
                    <label for="password">Пароль</label>
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
                <button type="submit" class="btn btn-success">Создать учителя</button>
                <a href="{{ route('admin.teachers.index') }}" class="btn btn-secondary">Назад</a>
            </div>
        </form>
    </section>
</div>

<script>
    document.querySelectorAll('[data-show-password]').forEach((toggle) => {
        const fields = toggle.dataset.showPassword
            .split(',')
            .map((selector) => document.querySelector(selector.trim()))
            .filter(Boolean);

        toggle.addEventListener('change', () => {
            fields.forEach((field) => {
                field.type = toggle.checked ? 'text' : 'password';
            });
        });
    });
</script>
@endsection
