@extends('layouts.app')

@section('content')
<div class="container">
    <div class="page-header">
        <div>
            <h1 class="page-title">Редактировать группу</h1>
            <p class="page-subtitle">{{ $group->name }}</p>
        </div>
    </div>

    <form action="{{ route('groups.update', $group) }}" method="POST" class="stack">
        @csrf
        @method('PATCH')

        <div class="form-grid">
            <div class="field">
                <label for="name">Название группы</label>
                <input id="name" type="text" name="name" value="{{ old('name', $group->name) }}" required>
                @error('name') <div class="error-message">{{ $message }}</div> @enderror
            </div>

            <div class="field field-full">
                <label for="description">Описание</label>
                <textarea id="description" name="description" rows="4">{{ old('description', $group->description) }}</textarea>
                @error('description') <div class="error-message">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="actions">
            <button type="submit" class="btn btn-success">Сохранить изменения</button>
            <a href="{{ route('groups.show', $group) }}" class="btn btn-secondary">Назад</a>
        </div>
    </form>
</div>
@endsection
