@extends('layouts.app')

@section('content')
<div class="container">
    <div class="page-header">
        <div>
            <h1 class="page-title">Профиль</h1>
            <p class="page-subtitle">Здесь можно изменить имя, email и пароль для входа. Удаление аккаунта находится в отдельном опасном разделе ниже.</p>
        </div>
    </div>

    <div class="stack">
        <div class="panel">
            @include('profile.partials.update-profile-information-form')
        </div>

        <div class="panel">
            @include('profile.partials.update-password-form')
        </div>

        <div class="panel">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</div>
@endsection
