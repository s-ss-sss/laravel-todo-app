<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Todo編集</title>
</head>
<body>
    <h1>Todo編集</h1>

    <form method="POST" action="{{ route('todos.update', $todo) }}">
        @csrf
        @method('PUT')

        <div>
            <label for="title">タイトル</label>

            <input
                id="title"
                name="title"
                type="text"
                value="{{ old('title', $todo->title) }}"
                required
            >

            @error('title')
                <p>{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="description">説明</label>

            <textarea
                id="description"
                name="description"
            >{{ old('description', $todo->description) }}</textarea>

            @error('description')
                <p>{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="due_date">期限日</label>

            <input
                id="due_date"
                name="due_date"
                type="date"
                value="{{ old('due_date', $todo->due_date?->format('Y-m-d')) }}"
            >

            @error('due_date')
                <p>{{ $message }}</p>
            @enderror
        </div>

        <button type="submit">更新</button>
    </form>

    <p>
        <a href="{{ route('todos.show', $todo) }}">Todo詳細へ戻る</a>
    </p>
</body>
</html>
