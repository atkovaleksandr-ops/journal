<section>
    <header>
        <h2>Данные аккаунта</h2>
        <p class="muted">Имя отображается в верхнем меню и внутри личного кабинета. Email используется для входа и восстановления пароля.</p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="stack">
        @csrf
        @method('patch')

        <div class="form-grid">
            <div class="field">
                <label for="name">Имя</label>
                <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
                <x-input-error class="mt-2" :messages="$errors->get('name')" />
            </div>

            <div class="field">
                <label for="email">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required autocomplete="username">
                <x-input-error class="mt-2" :messages="$errors->get('email')" />
            </div>
        </div>

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div class="alert alert-danger">
                Email пока не подтвержден.
                <button form="send-verification" class="btn btn-secondary" style="margin-left: 10px;">
                    Отправить письмо повторно
                </button>
            </div>

            @if (session('status') === 'verification-link-sent')
                <div class="alert alert-success">Ссылка подтверждения отправлена на ваш email.</div>
            @endif
        @endif

        <div class="actions">
            <button type="submit" class="btn btn-primary">Сохранить</button>

            @if (session('status') === 'profile-updated')
                <span class="badge">Сохранено</span>
            @endif
        </div>
    </form>
</section>
