@extends('layouts.app')

@section('title', 'アカウント設定')

@section('content')
    <section class="p-account">
        <header class="p-account__header">
            <p class="p-account__eyebrow">
                ACCOUNT
            </p>

            <h1 class="p-account__title">
                アカウント設定
            </h1>
        </header>

        <div class="p-account__sections">
            <section class="c-card p-account__section">
                <header class="p-account__section-header">
                    <h2 class="p-account__section-title">
                        プロフィール情報
                    </h2>

                    <p class="p-account__section-description">
                        ユーザー名とメールアドレスを変更できます。
                    </p>
                </header>

                @if (session('status') === 'profile-information-updated')
                    <div
                        class="c-alert c-alert--success"
                        role="status"
                    >
                        プロフィール情報を変更しました。
                    </div>
                @endif

                <form
                    class="c-form"
                    method="POST"
                    action="{{ route('user-profile-information.update') }}"
                >
                    @csrf
                    @method('PUT')

                    <div class="c-form__group">
                        <label
                            class="c-form__label"
                            for="name"
                        >
                            ユーザー名

                            <span class="c-form__required">
                                必須
                            </span>
                        </label>

                        <input
                            class="c-form__control @error('name', 'updateProfileInformation') is-invalid @enderror"
                            id="name"
                            name="name"
                            type="text"
                            value="{{ old('name', auth()->user()->name) }}"
                            autocomplete="name"
                            required
                            @error('name', 'updateProfileInformation')
                                aria-invalid="true"
                                aria-describedby="profile-name-error"
                            @enderror
                        >

                        @error('name', 'updateProfileInformation')
                            <p
                                class="c-form__error"
                                id="profile-name-error"
                            >
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="c-form__group">
                        <label
                            class="c-form__label"
                            for="email"
                        >
                            メールアドレス

                            <span class="c-form__required">
                                必須
                            </span>
                        </label>

                        <input
                            class="c-form__control @error('email', 'updateProfileInformation') is-invalid @enderror"
                            id="email"
                            name="email"
                            type="email"
                            value="{{ old('email', auth()->user()->email) }}"
                            autocomplete="email"
                            required
                            @error('email', 'updateProfileInformation')
                                aria-invalid="true"
                                aria-describedby="profile-email-error"
                            @enderror
                        >

                        @error('email', 'updateProfileInformation')
                            <p
                                class="c-form__error"
                                id="profile-email-error"
                            >
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="c-form__actions p-account__form-actions">
                        <button
                            class="c-button c-button--primary"
                            type="submit"
                        >
                            登録情報を変更
                        </button>
                    </div>
                </form>
            </section>

            <section class="c-card p-account__section">
                <header class="p-account__section-header">
                    <h2 class="p-account__section-title">
                        パスワード変更
                    </h2>

                    <p class="p-account__section-description">
                        現在のパスワードを確認して、新しいパスワードへ変更します。
                    </p>
                </header>

                @if (session('status') === 'password-updated')
                    <div
                        class="c-alert c-alert--success"
                        role="status"
                    >
                        パスワードを変更しました。
                    </div>
                @endif

                <form
                    class="c-form"
                    method="POST"
                    action="{{ route('user-password.update') }}"
                >
                    @csrf
                    @method('PUT')

                    <div class="c-form__group">
                        <label
                            class="c-form__label"
                            for="current_password"
                        >
                            現在のパスワード

                            <span class="c-form__required">
                                必須
                            </span>
                        </label>

                        <input
                            class="c-form__control @error('current_password', 'updatePassword') is-invalid @enderror"
                            id="current_password"
                            name="current_password"
                            type="password"
                            autocomplete="current-password"
                            required
                            @error('current_password', 'updatePassword')
                                aria-invalid="true"
                                aria-describedby="current-password-error"
                            @enderror
                        >

                        @error('current_password', 'updatePassword')
                            <p
                                class="c-form__error"
                                id="current-password-error"
                            >
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="c-form__group">
                        <label
                            class="c-form__label"
                            for="password"
                        >
                            新しいパスワード

                            <span class="c-form__required">
                                必須
                            </span>
                        </label>

                        <input
                            class="c-form__control @error('password', 'updatePassword') is-invalid @enderror"
                            id="password"
                            name="password"
                            type="password"
                            autocomplete="new-password"
                            required
                            @error('password', 'updatePassword')
                                aria-invalid="true"
                                aria-describedby="new-password-error"
                            @enderror
                        >

                        @error('password', 'updatePassword')
                            <p
                                class="c-form__error"
                                id="new-password-error"
                            >
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="c-form__group">
                        <label
                            class="c-form__label"
                            for="password_confirmation"
                        >
                            新しいパスワード確認

                            <span class="c-form__required">
                                必須
                            </span>
                        </label>

                        <input
                            class="c-form__control"
                            id="password_confirmation"
                            name="password_confirmation"
                            type="password"
                            autocomplete="new-password"
                            required
                        >
                    </div>

                    <div class="c-form__actions p-account__form-actions">
                        <button
                            class="c-button c-button--primary"
                            type="submit"
                        >
                            パスワードを変更
                        </button>
                    </div>
                </form>
            </section>

            <section class="c-card p-account__section p-account__section--danger">
                <header class="p-account__section-header">
                    <h2 class="p-account__section-title p-account__section-title--danger">
                        アカウント削除
                    </h2>

                    <p class="p-account__section-description">
                        アカウントを削除すると、このアカウントではログインできなくなります。
                    </p>
                </header>

                <div class="c-form">
                    <div class="c-form__group">
                        <label
                            class="c-form__label"
                            for="delete_current_password"
                        >
                            現在のパスワード

                            <span class="c-form__required">
                                必須
                            </span>
                        </label>

                        <input
                            class="c-form__control"
                            id="delete_current_password"
                            name="current_password"
                            type="password"
                            autocomplete="current-password"
                            disabled
                        >
                    </div>

                    <div class="c-form__actions p-account__form-actions">
                        <button
                            class="c-button c-button--danger-outline"
                            type="button"
                            disabled
                        >
                            アカウントを削除
                        </button>
                    </div>
                </div>
            </section>
        </div>
    </section>
@endsection
