# Laravel Todo App

## Overview
Laravelで構築したToDoアプリケーションです。  
Dockerで開発環境を構築し、認証・CRUD・検索機能を実装します。

## Features
### 認証機能
- ユーザー登録
- ログイン
- ログアウト

### タスク管理機能
- タスク一覧
- タスク詳細
- タスク登録
- タスク編集
- タスク削除
- タスク完了
- 並び替え

### 検索機能
- キーワード検索
- ステータス検索
- 期限検索

### アカウント管理機能
- プロフィール編集
- アカウント削除

## Transition
![Transition](./docs/transition.jpg)

## Stack
### Frontend
![HTML5](https://img.shields.io/badge/HTML5-E34F26?logo=html5&logoColor=white)
![SCSS](https://img.shields.io/badge/SCSS-CC6699?logo=sass&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?logo=javascript&logoColor=black)

### Backend
![PHP](https://img.shields.io/badge/PHP-777BB4?logo=php&logoColor=white)
![Laravel](https://img.shields.io/badge/Laravel-FF2D20?logo=laravel&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?logo=mysql&logoColor=white)

### Infrastructure
![Docker](https://img.shields.io/badge/Docker-2496ED?logo=docker&logoColor=white)
![Docker Compose](https://img.shields.io/badge/Docker%20Compose-2496ED?logo=docker&logoColor=white)
![Apache](https://img.shields.io/badge/Apache-D22128?logo=apache&logoColor=white)

### Others
![Composer](https://img.shields.io/badge/Composer-885630?logo=composer&logoColor=white)
![Git](https://img.shields.io/badge/Git-F05032?logo=git&logoColor=white)
![GitHub](https://img.shields.io/badge/GitHub-181717?logo=github&logoColor=white)

## Directory
```bash
todo/
├── docker/
│   ├── apache/         # Apache設定
│   ├── php/            # PHP設定
│   └── mysql/          # MySQL設定
├── src/
│   ├── app/            # アプリケーションロジック
│   ├── database/       # マイグレーション・Seeder
│   ├── public/         # 公開ディレクトリ
│   ├── resources/      # Blade・SCSS・JavaScript
│   ├── routes/         # ルーティング
│   └── tests/          # テストコード
├── docker-compose.yml  # Docker Compose設定
├── .env.example        # 環境変数サンプル
├── .gitignore
└── README.md
```

## Database
### users
| Column | Type | Null | Key | Default | Description |
|---|---|---|---|---|---|
| id | BIGINT | NO | PK | AUTO_INCREMENT | ユーザーID |
| name | VARCHAR(255) | NO | - | - | ユーザー名 |
| email | VARCHAR(255) | NO | UQ | - | メールアドレス |
| email_verified_at | TIMESTAMP | YES | - | NULL | メール認証日時 |
| password | VARCHAR(255) | NO | - | - | パスワード |
| remember_token | VARCHAR(100) | YES | - | NULL | ログイン保持トークン |
| created_at | TIMESTAMP | YES | - | NULL | 登録日時 |
| updated_at | TIMESTAMP | YES | - | NULL | 更新日時 |
| deleted_at | TIMESTAMP | YES | - | NULL | 削除日時 |

### todos
| Column | Type | Null | Key | Default | Description |
|---|---|---|---|---|---|
| id | BIGINT | NO | PK | AUTO_INCREMENT | ToDo ID |
| user_id | BIGINT | NO | FK | - | ユーザーID |
| title | VARCHAR(255) | NO | - | - | タイトル |
| description | TEXT | YES | - | NULL | 説明 |
| is_completed | BOOLEAN | NO | - | false | 完了フラグ |
| due_date | DATE | YES | - | NULL | 期限日 |
| sort_order | INT | NO | - | 0 | 表示順 |
| created_at | TIMESTAMP | YES | - | NULL | 登録日時 |
| updated_at | TIMESTAMP | YES | - | NULL | 更新日時 |
| deleted_at | TIMESTAMP | YES | - | NULL | 削除日時 |

## ER
```mermaid
erDiagram

    users {
        bigint id
        varchar name
        varchar email
        timestamp email_verified_at
        varchar password
        varchar remember_token
        timestamp created_at
        timestamp updated_at
        timestamp deleted_at
    }

    todos {
        bigint id
        bigint user_id
        varchar title
        text description
        boolean is_completed
        date due_date
        int sort_order
        timestamp created_at
        timestamp updated_at
        timestamp deleted_at
    }

    users ||--o{ todos : ""
```

## License
MIT
