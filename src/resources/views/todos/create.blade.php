<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Todo登録</title>
</head>
<body>
    <h1>Todo登録</h1>

    <form method="POST" action="{{ route('todos.store') }}">
        @csrf

        <div>
            <label for="title">タイトル</label>

            <input
                id="title"
                name="title"
                type="text"
                value="{{ old('title') }}"
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
            >{{ old('description') }}</textarea>

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
                value="{{ old('due_date') }}"
            >

            @error('due_date')
                <p>{{ $message }}</p>
            @enderror
        </div>

        <button type="submit">登録</button>
    </form>

    <p>
        <a href="{{ route('todos.index') }}">Todo一覧へ戻る</a>
    </p>
</body>
</html>
