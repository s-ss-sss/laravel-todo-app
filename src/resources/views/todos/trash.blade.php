@extends('layouts.app')

@section('title', 'ゴミ箱')

@section('content')
    <section class="p-todo-trash">
        <header class="p-page-header">
            <div>
                <p class="p-page-header__eyebrow">TRASH</p>
                <h1 class="p-page-header__title">ゴミ箱</h1>
            </div>

            <div class="p-todo-trash__header-actions">
                <a
                    class="c-button c-button--secondary"
                    href="{{ route('todos.index') }}"
                >
                    Todo一覧へ戻る
                </a>

                @if ($todos->total() > 0)
                    <form
                        method="POST"
                        action="{{ route('todos.trash.empty') }}"
                        onsubmit="return confirm('ゴミ箱内のTodoをすべて完全に削除します。この操作は取り消せません。')"
                    >
                        @csrf
                        @method('DELETE')

                        <button
                            class="c-button c-button--danger-outline"
                            type="submit"
                        >
                            ゴミ箱を空にする
                        </button>
                    </form>
                @endif
            </div>
        </header>

        <div class="c-card c-card--flat p-todo-trash__list">
            @forelse ($todos as $todo)
                <article class="p-todo-trash__item">
                    <div class="p-todo-trash__content">
                        <h2 class="p-todo-trash__title">
                            {{ $todo->title }}
                        </h2>

                        <p class="p-todo-trash__date">
                            削除日時：{{ $todo->deleted_at?->format('Y年m月d日 H:i') }}
                        </p>
                    </div>

                    <div class="p-todo-trash__actions">
                        <form
                            method="POST"
                            action="{{ route('todos.restore', $todo->id) }}"
                        >
                            @csrf
                            @method('PATCH')

                            <button
                                class="c-button c-button--primary"
                                type="submit"
                            >
                                復元する
                            </button>
                        </form>

                        <form
                            method="POST"
                            action="{{ route('todos.force-delete', $todo->id) }}"
                            onsubmit="return confirm('このTodoを完全に削除しますか？この操作は取り消せません。')"
                        >
                            @csrf
                            @method('DELETE')

                            <button
                                class="c-button c-button--danger"
                                type="submit"
                            >
                                完全に削除
                            </button>
                        </form>
                    </div>
                </article>
            @empty
                <p class="p-todo-trash__empty">
                    ゴミ箱は空です。
                </p>
            @endforelse
        </div>

        {{ $todos->links() }}
    </section>
@endsection
