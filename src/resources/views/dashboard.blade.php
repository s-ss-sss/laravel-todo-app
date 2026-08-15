@extends('layouts.app')

@section('title', 'ダッシュボード')

@section('content')
    <section class="p-dashboard">
        <header class="p-dashboard__header">
            <p class="p-dashboard__eyebrow">ACCOUNT</p>
            <h1 class="p-dashboard__title">ダッシュボード</h1>
        </header>

        <div class="c-card p-dashboard__body">
            <dl class="p-dashboard__list">
                <div class="p-dashboard__item">
                    <dt class="p-dashboard__label">ユーザー名</dt>
                    <dd class="p-dashboard__value">
                        {{ auth()->user()->name }}
                    </dd>
                </div>

                <div class="p-dashboard__item">
                    <dt class="p-dashboard__label">メールアドレス</dt>
                    <dd class="p-dashboard__value">
                        {{ auth()->user()->email }}
                    </dd>
                </div>
            </dl>
        </div>
    </section>
@endsection
