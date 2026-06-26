<x-guest-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-white">Новый пароль</h1>
        <p class="mt-2 text-sm text-gray-600">Придумайте новый пароль для входа в личный кабинет. После сохранения вы сможете войти с ним на странице авторизации.</p>
    </div>

    <form method="POST" action="{{ route('password.store', [], false) }}">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" value="Новый пароль" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password_confirmation" value="Повторите пароль" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-5">
            <x-primary-button>
                Сохранить пароль
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
