@extends('layouts.app')

@section('title', 'ユーザー登録')

@section('content')
    <div class="p-auth">
        <section class="c-card p-auth__card">
            <header class="p-auth__header">
                <p class="p-auth__eyebrow">CREATE ACCOUNT</p>
                <h1 class="p-auth__title">ユーザー登録</h1>
            </header>

            <form class="c-form" method="POST" action="{{ route('register') }}">
                @csrf

                <div class="c-form__group">
                    <label class="c-form__label" for="name">
                        ユーザー名
                    </label>
                    <input
                        class="c-form__control"
                        id="name"
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        autocomplete="name"
                        required
                        autofocus
                    >

                    @error('name')
                        <p class="c-form__error">{{ $message }}</p>
                    @enderror
                </div>

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
                        autocomplete="new-password"
                        required
                    >

                    @error('password')
                        <p class="c-form__error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="c-form__group">
                    <label class="c-form__label" for="password_confirmation">
                        パスワード確認
                    </label>
                    <input
                        class="c-form__control"
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        autocomplete="new-password"
                        required
                    >
                </div>

                <button
                    class="c-button c-button--primary"
                    type="submit"
                >
                    登録
                </button>
            </form>

            <p class="p-auth__footer">
                すでにアカウントをお持ちの方
                <a
                    class="p-auth__footer-link"
                    href="{{ route('login') }}"
                >
                    ログイン画面へ
                </a>
            </p>
        </section>
    </div>
@endsection
