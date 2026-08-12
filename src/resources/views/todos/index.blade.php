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

    <form method="GET" action="{{ route('todos.index') }}">
        <div>
            <label for="keyword">キーワード</label>

            <input
                id="keyword"
                name="keyword"
                type="search"
                value="{{ request('keyword') }}"
            >

            @error('keyword')
                <p>{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="status">状態</label>

            <select id="status" name="status">
                <option value="">すべて</option>

                <option
                    value="completed"
                    @selected(request('status') === 'completed')
                >
                    完了
                </option>

                <option
                    value="incomplete"
                    @selected(request('status') === 'incomplete')
                >
                    未完了
                </option>
            </select>

            @error('status')
                <p>{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="due_date">期限日</label>

            <input
                id="due_date"
                name="due_date"
                type="date"
                value="{{ request('due_date') }}"
            >

            @error('due_date')
                <p>{{ $message }}</p>
            @enderror
        </div>

        <button type="submit">検索</button>

        <a href="{{ route('todos.index') }}">
            条件をクリア
        </a>
    </form>

    @if ($todos->isEmpty())
        @if (
            request()->filled('keyword')
            || request()->filled('status')
            || request()->filled('due_date')
        )
            <p>検索条件に一致するTodoはありません。</p>
        @else
            <p>Todoはまだありません。</p>
        @endif
    @else
        <ul>
            @foreach ($todos as $todo)
                <li>
                    <a href="{{ route('todos.show', $todo) }}">
                        {{ $todo->title }}
                    </a>

                    <span>
                        {{ $todo->is_completed ? '完了' : '未完了' }}
                    </span>

                    @if (
                        ! request()->filled('keyword')
                        && ! request()->filled('status')
                        && ! request()->filled('due_date')
                    )
                        <form
                            method="POST"
                            action="{{ route('todos.move-up', $todo) }}"
                        >
                            @csrf
                            @method('PATCH')

                            <button type="submit">上へ</button>
                        </form>

                        <form
                            method="POST"
                            action="{{ route('todos.move-down', $todo) }}"
                        >
                            @csrf
                            @method('PATCH')

                            <button type="submit">下へ</button>
                        </form>
                    @endif
                </li>
            @endforeach
        </ul>
    @endif
</body>
</html>
