@extends('layouts.app')

@section('title', 'ログイン')

@section('content')
    <div class="p-auth">
        <section class="c-card p-auth__card">
            <header class="p-auth__header">
                <p class="p-auth__eyebrow">WELCOME BACK</p>
                <h1 class="p-auth__title">ログイン</h1>
            </header>

            <form class="c-form" method="POST" action="{{ route('login') }}">
                @csrf

                <div class="c-form__group">
                    <label class="c-form__label" for="email">
                        メールアドレス
                    </label>
                    <input
                        class="c-form__control"
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        autocomplete="email"
                        required
                        autofocus
                    >

                    @error('email')
                        <p class="c-form__error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="c-form__group">
                    <label class="c-form__label" for="password">
                        パスワード
                    </label>
                    <input
                        class="c-form__control"
                        id="password"
                        type="password"
                        name="password"
                        autocomplete="current-password"
                        required
                    >
                </div>

                <label class="c-form__check">
                    <input type="checkbox" name="remember">
                    ログイン状態を保持する
                </label>

                <button
                    class="c-button c-button--primary"
                    type="submit"
                >
                    ログイン
                </button>
            </form>

            <p class="p-auth__footer">
                アカウントをお持ちでない方
                <a
                    class="p-auth__footer-link"
                    href="{{ route('register') }}"
                >
                    ユーザー登録へ
                </a>
            </p>
        </section>
    </div>
@endsection
