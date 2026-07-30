<form method="POST" action="{{ route('register') }}">
    @csrf

    <div>
        <label for="name">ユーザー名</label>
        <input
            id="name"
            type="text"
            name="name"
            value="{{ old('name') }}"
            required
            autofocus
        >

        @error('name')
        <p>{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="email">メールアドレス</label>
        <input
            id="email"
            type="email"
            name="email"
            value="{{ old('email') }}"
            required
        >

        @error('email')
        <p>{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="password">パスワード</label>
        <input
            id="password"
            type="password"
            name="password"
            required
        >

        @error('password')
        <p>{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="password_confirmation">パスワード確認</label>
        <input
            id="password_confirmation"
            type="password"
            name="password_confirmation"
            required
        >
    </div>

    <button type="submit">登録</button>
</form>

<a href="{{ route('login') }}">ログイン画面へ</a>
