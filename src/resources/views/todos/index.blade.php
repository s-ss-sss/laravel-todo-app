<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Todo一覧</title>
</head>
<body>
    <h1>Todo一覧</h1>

    <p>
        <a href="{{ route('todos.create') }}">新しいTodoを登録</a>
    </p>

    @if ($todos->isEmpty())
        <p>Todoはまだありません。</p>
    @else
        <ul>
            @foreach ($todos as $todo)
                <li>{{ $todo->title }}</li>
            @endforeach
        </ul>
    @endif
</body>
</html>
