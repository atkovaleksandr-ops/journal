<x-guest-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-white">Подтвердите пароль</h1>
        <p class="mt-2 text-sm text-gray-600">Это защищенное действие. Для продолжения введите пароль от текущего аккаунта.</p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <div>
            <x-input-label for="password" value="Пароль" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex justify-end mt-5">
            <x-primary-button>
                Подтвердить
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
