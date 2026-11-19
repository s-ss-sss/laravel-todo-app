# Deployment Guide

## 概要

Laravel Todo AppをConoHa WINGへ手動デプロイする手順です。

本アプリはLaravel本体とWeb公開ディレクトリを分離し、`/todo`サブディレクトリ配下で公開しています。

この手順書では、初回デプロイ後の通常更新を中心に説明します。

## 本番環境

- Hosting: ConoHa WING
- PHP: 8.5
- Framework: Laravel 13
- Database: MySQL
- Frontend build: Vite
- Web URL: `https://dolzap.conohawing.com/todo/`

## ディレクトリ構成

Laravel本体はWebから直接アクセスできない場所へ配置し、`src/public/`の内容だけを公開ディレクトリへ配置します。

```text
/home/<SSH_USER>/
├── laravel-todo/               # Laravel本体
└── public_html/
    └── dolzap.conohawing.com/
        └── todo/               # Web公開ファイル
```

## コマンド内のプレースホルダー

実行時は、各値を実際の接続情報へ置き換えます。

接続情報や秘密鍵、DBパスワードはGitへ登録しません。

| 値 | 内容 |
|---|---|
| `<SSH_USER>` | ConoHaのSSHユーザー名 |
| `<SSH_HOST>` | ConoHaのSSHホスト名 |
| `<SSH_PORT>` | SSHポート番号 |
| `<APP_PATH>` | Laravel本体の配置先 |
| `<PUBLIC_PATH>` | Web公開ファイルの配置先 |

## 通常デプロイ

### 1. mainを最新化

```bash
git switch main
git pull origin main
git status
```

### 2. Laravelテスト

```bash
docker compose exec app php artisan test
```

### 3. Pint

```bash
docker compose exec app ./vendor/bin/pint --test
```

### 4. Viteビルド

```bash
docker compose run --rm node npm ci
docker compose run --rm node npm run build
```

以下が生成されていることを確認します。

```text
src/public/build/manifest.json
src/public/build/assets/
```

### 5. Laravel本体のdry-run

```bash
rsync -avzn \
  -e "ssh -p <SSH_PORT>" \
  --exclude-from='deploy/rsync-app-exclude.txt' \
  src/ \
  <SSH_USER>@<SSH_HOST>:<APP_PATH>/
```

以下が転送予定に含まれないことを確認します。

- `.env`
- `vendor/`
- `node_modules/`
- `bootstrap/cache/*.php`
- `public/hot`
- `public/fonts-manifest.dev.json`
- 本番ログ・セッション・キャッシュ

### 6. 公開ファイルのdry-run

```bash
rsync -avzn \
  -e "ssh -p <SSH_PORT>" \
  --exclude-from='deploy/rsync-public-exclude.txt' \
  src/public/ \
  <SSH_USER>@<SSH_HOST>:<PUBLIC_PATH>/
```

`build/manifest.json`と`build/assets/`は転送対象です。

以下が転送予定に含まれないことを確認します。

- `index.php`
- `hot`
- `fonts-manifest.dev.json`

### 7. Laravel本体を転送

```bash
rsync -avz \
  -e "ssh -p <SSH_PORT>" \
  --exclude-from='deploy/rsync-app-exclude.txt' \
  src/ \
  <SSH_USER>@<SSH_HOST>:<APP_PATH>/
```

### 8. 公開ファイルを転送

本番専用ファイルやサーバー上で生成されたファイルを誤って削除する可能性があるため、初期運用では`--delete`を使用しません。

```bash
rsync -avz \
  -e "ssh -p <SSH_PORT>" \
  --exclude-from='deploy/rsync-public-exclude.txt' \
  src/public/ \
  <SSH_USER>@<SSH_HOST>:<PUBLIC_PATH>/
```

### 9. 本番サーバーへSSH接続

```bash
ssh -p <SSH_PORT> <SSH_USER>@<SSH_HOST>
cd <APP_PATH>
```

### 10. Composerパッケージを更新

```bash
php ~/bin/composer install \
  --no-dev \
  --prefer-dist \
  --optimize-autoloader \
  --no-interaction
```

### 11. マイグレーション確認

```bash
php artisan migrate:status
```

未実行のマイグレーションがある場合は以下を実行します。

```bash
php artisan migrate --force
```

### 12. キャッシュを再生成

ConoHa WINGでは`route:cache`実行後に405エラーが発生したため、ルートキャッシュは作成しません。

```bash
php artisan route:clear
php artisan config:clear
php artisan event:clear
php artisan view:clear

php artisan config:cache
php artisan event:cache
php artisan view:cache
```

## デプロイ後の確認

### HTTP確認

```bash
curl -sSL \
  -o /dev/null \
  -w "HTTP %{http_code}\n最終URL: %{url_effective}\n" \
  https://dolzap.conohawing.com/todo/
```

以下が期待結果です。

```text
HTTP 200
最終URL: https://dolzap.conohawing.com/todo/login
```

### ブラウザ確認

- ユーザー登録
- ログイン・ログアウト
- Todo登録・編集・完了
- 検索・並び替え
- ゴミ箱・復元
- アカウント設定
- CSS・JavaScriptの読み込み
- ブラウザコンソールにエラーがないこと

### Laravelログ

```bash
tail -n 100 storage/logs/laravel.log
```

### ConoHaエラーログ

ConoHa管理画面、またはSSHでドメイン別の`error_log`を確認します。

```bash
tail -n 100 ~/logs/<DOMAIN>/error_log
```

## ConoHa WING固有の注意点

### 公開側index.php

公開側の`index.php`は、Laravel本体の絶対パスを参照する本番専用ファイルです。

ローカルの`src/public/index.php`で上書きしないよう、`deploy/rsync-public-exclude.txt`で除外しています。

### .htaccess

ConoHa WINGでは`Options -MultiViews`が許可されていないため、`.htaccess`では`Options -Indexes`のみを設定しています。

### bootstrap/cache

ローカルの`bootstrap/cache/*.php`には開発用パッケージの情報が含まれる可能性があるため、本番へ転送しません。

### ルートキャッシュ

以下は使用しません。

```bash
php artisan route:cache
php artisan optimize
```

## トラブルシューティング

### 500 Internal Server Error

確認項目：

- Laravelログ
- ConoHaの`error_log`
- 公開側`index.php`の参照パス
- `.htaccess`
- `storage`と`bootstrap/cache`の権限
- `vendor/autoload.php`の存在

### 405 Method Not Allowed

ルートキャッシュを削除します。

```bash
php artisan route:clear
```

### `PailServiceProvider`が見つからない

開発環境で生成されたパッケージキャッシュが、本番サーバーへ転送されている可能性があります。

```bash
cd <APP_PATH>
rm -f bootstrap/cache/*.php
php ~/bin/composer install \
  --no-dev \
  --prefer-dist \
  --optimize-autoloader \
  --no-interaction
php artisan package:discover --ansi
```

再発防止のため、`bootstrap/cache/*.php`が`deploy/rsync-app-exclude.txt`で除外されていることも確認します。

### CSS・JavaScriptが読み込まれない

ローカルでViteビルドを実行し、生成物が公開ディレクトリへ転送されていることを確認します。

```bash
docker compose run --rm node npm run build
```

確認対象：

- `src/public/build/manifest.json`
- `src/public/build/assets/`
- 本番公開ディレクトリの`build/manifest.json`
- 本番公開ディレクトリの`build/assets/`

## 初回デプロイ

本番`.env`やDB接続情報はGit管理しません。

初回は通常デプロイに加えて、以下が必要です。

- Laravel本体用ディレクトリの作成
- 公開ディレクトリの作成
- 本番`.env`の作成
- Composerの準備
- `APP_KEY`の生成
- 公開側`index.php`の修正
- DBの作成
- 初回マイグレーション
- ファイル権限の設定

## バックアップ

- ConoHa WINGの自動バックアップを確認する
- DB変更前にSQLバックアップを取得する
- 本番`.env`を安全な場所へ保管する
- バックアップファイルは公開ディレクトリへ置かない

## ロールバック

本番では`migrate:fresh`や`db:wipe`を実行しません。

障害時は以下を確認します。

1. メンテナンスモード
2. 現在の状態をバックアップ
3. 直前のGitコミットを再デプロイ
4. 必要に応じてDBを復元
5. キャッシュを再生成
6. スモークテスト
7. メンテナンスモード解除
