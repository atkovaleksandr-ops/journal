<x-guest-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-white">Подтверждение email</h1>
        <p class="mt-2 text-sm text-gray-600">Мы отправили письмо со ссылкой подтверждения. Откройте письмо и перейдите по ссылке, чтобы завершить настройку аккаунта.</p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-green-600">
            Новая ссылка подтверждения отправлена на ваш email.
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between gap-3">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <x-primary-button>
                Отправить письмо еще раз
            </x-primary-button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="text-sm rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Выйти
            </button>
        </form>
    </div>
</x-guest-layout>
