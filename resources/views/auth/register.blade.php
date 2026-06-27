<x-guest-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-white">Регистрация</h1>
        <p class="mt-2 text-sm text-gray-600">Регистрация создает личный кабинет студента. Преподавателей добавляет администратор.</p>
    </div>

    <form method="POST" action="{{ route('register', [], false) }}" class="auth-form">
        @csrf

        <div class="auth-field">
            <label for="name">Имя</label>
            <input id="name" class="auth-input" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name">
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="auth-field">
            <label for="email">Email</label>
            <input id="email" class="auth-input" type="email" name="email" value="{{ old('email') }}" required autocomplete="username">
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="auth-field">
            <label for="password">Пароль</label>
            <input id="password" class="auth-input" type="password" name="password" required autocomplete="new-password">
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="auth-field">
            <label for="password_confirmation">Повторите пароль</label>
            <input id="password_confirmation" class="auth-input" type="password" name="password_confirmation" required autocomplete="new-password">
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="auth-options auth-options-single">
            <label for="show_password" class="remember-control">
                <input id="show_password" type="checkbox" data-show-password="#password, #password_confirmation">
                <span>Показать пароль</span>
            </label>
        </div>

        <div class="auth-submit-row">
            <button type="submit" class="auth-submit">Зарегистрироваться</button>
        </div>

        <div class="auth-alt-action">
            <span>Уже есть аккаунт?</span>
            <a href="{{ route('login', [], false) }}">Войти</a>
        </div>
    </form>

    <script>
        document.querySelectorAll('[data-show-password]').forEach((toggle) => {
            toggle.addEventListener('change', () => {
                document.querySelectorAll(toggle.dataset.showPassword).forEach((input) => {
                    input.type = toggle.checked ? 'text' : 'password';
                });
            });
        });
    </script>
</x-guest-layout>
