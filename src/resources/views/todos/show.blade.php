<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Todo詳細</title>
</head>
<body>
    <h1>Todo詳細</h1>

    <dl>
        <dt>タイトル</dt>
        <dd>{{ $todo->title }}</dd>

        <dt>説明</dt>
        <dd>{{ $todo->description ?? '説明はありません。' }}</dd>

        <dt>期限日</dt>
        <dd>
            {{ $todo->due_date?->format('Y/m/d') ?? '期限はありません。' }}
        </dd>

        <dt>状態</dt>
        <dd>{{ $todo->is_completed ? '完了' : '未完了' }}</dd>
    </dl>

    <form
        method="POST"
        action="{{ route('todos.toggle-completion', $todo) }}"
    >
        @csrf
        @method('PATCH')

        <button type="submit">
            {{ $todo->is_completed ? '未完了に戻す' : '完了にする' }}
        </button>
    </form>

    <p>
        <a href="{{ route('todos.edit', $todo) }}">このTodoを編集</a>
    </p>

    <form
        method="POST"
        action="{{ route('todos.destroy', $todo) }}"
        onsubmit="return confirm('このTodoを削除しますか？')"
    >
        @csrf
        @method('DELETE')

        <button type="submit">このTodoを削除</button>
    </form>

    <p>
        <a href="{{ route('todos.index') }}">Todo一覧へ戻る</a>
    </p>
</body>
</html>
