@extends('layouts.app')

@section('content')
<div class="container">
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ $subject->name }}</h1>
            <p class="page-subtitle">{{ $subject->description ?: 'Описание пока не добавлено.' }}</p>
        </div>

        <div class="actions">
            <a href="{{ route('subjects.edit', $subject) }}" class="btn btn-warning">Редактировать</a>
            <a href="{{ route('groups.attendance', $subject->group_id) }}" class="btn btn-primary">Открыть журнал</a>
            <a href="{{ route('subjects.index') }}" class="btn btn-secondary">Все предметы</a>
            <form action="{{ route('subjects.destroy', $subject) }}" method="POST" onsubmit="return confirm('Удалить предмет?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Удалить</button>
            </form>
        </div>
    </div>

    <div class="panel">
        <span class="badge">Группа: {{ $subject->group->name ?? 'не указана' }}</span>
    </div>
</div>
@endsection
