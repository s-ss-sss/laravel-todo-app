<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        @yield('title', 'Todo App')
    </title>

    @vite([
        'resources/scss/app.scss',
        'resources/js/app.js',
    ])
</head>
<body>
    <header class="l-header">
        <div class="l-header__inner">
            <a
                class="l-header__logo"
                href="{{ route('home') }}"
            >
                TODO
            </a>

            <nav class="l-header__nav" aria-label="メインナビゲーション">
                @auth
                    <a
                        class="l-header__link {{ request()->routeIs('todos.*') ? 'is-active' : '' }}"
                        href="{{ route('todos.index') }}"
                    >
                        Todo一覧
                    </a>

                    <a
                        class="l-header__link {{ request()->routeIs('dashboard') ? 'is-active' : '' }}"
                        href="{{ route('dashboard') }}"
                    >
                        アカウント
                    </a>

                    <form
                        class="l-header__form"
                        method="POST"
                        action="{{ route('logout') }}"
                    >
                        @csrf

                        <button
                            class="c-button c-button--primary c-button--small"
                            type="submit"
                        >
                            ログアウト
                        </button>
                    </form>
                @else
                    <a
                        class="l-header__link {{ request()->routeIs('login') ? 'is-active' : '' }}"
                        href="{{ route('login') }}"
                    >
                        ログイン
                    </a>

                    <a
                        class="c-button c-button--primary c-button--small"
                        href="{{ route('register') }}"
                    >
                        ユーザー登録
                    </a>
                @endauth
            </nav>
        </div>
    </header>

    <main class="l-main">
        <div class="l-main__inner">
            @yield('content')
        </div>
    </main>
</body>
</html>
