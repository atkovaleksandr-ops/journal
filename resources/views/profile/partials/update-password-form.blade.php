<section>
    <header>
        <h2>Смена пароля</h2>
        <p class="muted">Используйте пароль, который сложно угадать. После смены новый пароль будет нужен при следующем входе.</p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="stack">
        @csrf
        @method('put')

        <div class="form-grid">
            <div class="field">
                <label for="update_password_current_password">Текущий пароль</label>
                <input id="update_password_current_password" name="current_password" type="password" autocomplete="current-password">
                <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
            </div>

            <div class="field">
                <label for="update_password_password">Новый пароль</label>
                <input id="update_password_password" name="password" type="password" autocomplete="new-password">
                <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
            </div>

            <div class="field">
                <label for="update_password_password_confirmation">Повторите пароль</label>
                <input id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password">
                <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
            </div>
        </div>

        <div class="actions">
            <button type="submit" class="btn btn-primary">Обновить пароль</button>

            @if (session('status') === 'password-updated')
                <span class="badge">Пароль обновлен</span>
            @endif
        </div>
    </form>
</section>
