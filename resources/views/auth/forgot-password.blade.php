<x-guest-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-white">Восстановление пароля</h1>
        <p class="mt-2 text-sm text-gray-600">Введите email от вашего аккаунта. Если такой пользователь есть в системе, Journal отправит ссылку для создания нового пароля.</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email', [], false) }}">
        @csrf

        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4 text-sm text-gray-600">
            Подсказка: используйте тот email, который был указан при регистрации или при создании учетной записи преподавателя.
        </div>

        <div class="flex items-center justify-end mt-5">
            <x-primary-button>
                Отправить ссылку
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
