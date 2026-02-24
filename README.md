# タスク管理 Web アプリ（Laravel + Docker + AI アドバイス）

認証コードで保護されたタスク管理アプリです。未完了タスクに対して OpenAI がアドバイスを表示します。データは Supabase（`tb_tasks`）で管理します。

## 技術スタック

- **Laravel 11** / PHP 8.2
- **Docker**（nginx + php-fpm）
- **Supabase**（tb_tasks: id, task_content, due_text, status）
- **OpenAI API**（gpt-4.1-mini / responses）

## ローカル開発（Docker）

1. リポジトリをクローンし、`.env` を用意します。

```bash
cp .env.example .env
# .env を編集: AUTH_CODE, SUPABASE_URL, SUPABASE_PUBLIC_KEY, OPENAI_API_KEY
# APP_KEY は次で生成
```

2. APP_KEY を生成して `.env` の `APP_KEY=` に貼り付けます。

```bash
docker run --rm php:8.2-cli php -r "echo 'base64:' . base64_encode(random_bytes(32)) . PHP_EOL;"
```

3. コンテナを起動

```bash
docker compose up -d
```

4. ブラウザで **http://localhost:8080** を開き、`.env` の `AUTH_CODE` を入力してアクセスします。

## 環境変数

| 変数名 | 説明 |
|--------|------|
| `AUTH_CODE` | トップページで入力する認証コード（必須） |
| `SUPABASE_URL` | Supabase プロジェクト URL |
| `SUPABASE_PUBLIC_KEY` | Supabase anon key |
| `OPENAI_API_KEY` | OpenAI API キー |
| `APP_KEY` | Laravel の暗号化キー（`php artisan key:generate` で生成） |

## Supabase テーブル

`tb_tasks` を用意してください。

- `id` … 主キー（auto）
- `task_content` … タスク内容（テキスト）
- `due_text` … 予定日（テキスト、例: 2025-02-24）
- `status` … 0=未完了, 1=完了

## 仕様概要

- **トップ** … 認証コード入力。正しければダッシュボードへ。
- **ダッシュボード** … 未完了タスクを Supabase から取得し、OpenAI で「直近で取り組むべき1タスク」のアドバイスを表示。`reference/demo.php` のロジックに準拠。
- **タスク一覧** … ID 順表示。完了（status=1）はグレーアウト。未完了は「完了」ボタンで status=1 に更新。
- **新規登録** … タスクと予定日を入力して `tb_tasks` に登録。

## GitHub → AWS App Runner デプロイ

`main` ブランチへマージすると、GitHub Actions で Docker イメージをビルドし、ECR に push して App Runner にデプロイされます。

### 事前準備

1. **ECR リポジトリ**  
   - 名前: `task-app`（または `.github/workflows/deploy-apprunner.yml` の `ECR_REPOSITORY` に合わせて変更）

2. **App Runner 用 IAM**  
   - ECR からイメージを pull するための **アクセスロール** の ARN を取得  
   - 必要なら App Runner サービスをコンソールで一度作成し、同じサービス名・リージョンを使う

3. **GitHub リポジトリの Secrets**

   - `AWS_ACCESS_KEY_ID` / `AWS_SECRET_ACCESS_KEY` … ECR と App Runner を操作する IAM ユーザー
   - `APP_RUNNER_ACCESS_ROLE_ARN` … App Runner の ECR アクセス用ロール ARN
   - `AUTH_CODE` … 本番の認証コード
   - `APP_KEY` … Laravel の `APP_KEY`（本番用に生成した値）
   - `SUPABASE_URL` / `SUPABASE_PUBLIC_KEY` / `OPENAI_API_KEY` … 本番用の値

### ワークフロー

- トリガー: `push` が `main` に乗ったとき
- 処理: `Dockerfile.apprunner` でビルド → ECR に push → App Runner のサービスを更新（イメージ差し替え＋上記環境変数で更新）

リージョンやサービス名を変える場合は `.github/workflows/deploy-apprunner.yml` の `env.AWS_REGION` と `APP_RUNNER_SERVICE` を編集してください。

## 参考

- AI アドバイスと Supabase 取得ロジックの元ネタ: `reference/demo.php`
