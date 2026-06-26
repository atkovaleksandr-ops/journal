@extends('layouts.app')

@section('content')
<div class="container">
    <div class="page-header">
        <div>
            <h1 class="page-title">Создать группу</h1>
            <p class="page-subtitle">Группа объединяет студентов и предметы, по ней открывается журнал уроков.</p>
        </div>
    </div>

    <form action="{{ route('groups.store') }}" method="POST" class="stack">
        @csrf

        <div class="form-grid">
            <div class="field">
                <label for="name">Название группы</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" placeholder="Например: IS-21" required>
                @error('name') <div class="error-message">{{ $message }}</div> @enderror
            </div>

            <div class="field field-full">
                <label for="description">Описание</label>
                <textarea id="description" name="description" rows="4" placeholder="Краткая заметка о группе">{{ old('description') }}</textarea>
                @error('description') <div class="error-message">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="actions">
            <button type="submit" class="btn btn-success">Сохранить</button>
            <a href="{{ route('groups.index') }}" class="btn btn-secondary">Назад</a>
        </div>
    </form>
</div>
@endsection
