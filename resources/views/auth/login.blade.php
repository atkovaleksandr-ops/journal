<x-guest-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-white">Вход в Journal</h1>
        <p class="mt-2 text-sm text-gray-600">Войдите как администратор, преподаватель или студент, чтобы открыть личный кабинет.</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login', [], false) }}" class="auth-form">
        @csrf

        <div class="auth-field">
            <label for="email">Email</label>
            <input id="email" class="auth-input" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="auth-field">
            <label for="password">Пароль</label>
            <input id="password" class="auth-input" type="password" name="password" required autocomplete="current-password">
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <label for="show_password" class="remember-control">
            <input id="show_password" type="checkbox" data-show-password="#password">
            <span>Показать пароль</span>
        </label>

        <div class="auth-actions">
            <label for="remember_me" class="remember-control">
                <input id="remember_me" type="checkbox" name="remember" value="1">
                <span>Запомнить меня</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request', [], false) }}">
                    Забыли пароль?
                </a>
            @endif
        </div>

        <div class="auth-submit-row">
            <button type="submit" class="auth-submit">Войти</button>
        </div>

        <div class="auth-footer">
            Нет аккаунта?
            <a href="{{ route('register', [], false) }}">Зарегистрироваться</a>
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
