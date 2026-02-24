<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'タスク管理')</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; margin: 0; padding: 1rem; background: #f5f5f5; }
        .container { max-width: 720px; margin: 0 auto; }
        .card { background: #fff; border-radius: 8px; padding: 1.5rem; margin-bottom: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,.08); }
        h1 { margin: 0 0 1rem; font-size: 1.5rem; color: #333; }
        .nav { margin-bottom: 1rem; }
        .nav a { margin-right: 1rem; color: #2563eb; text-decoration: none; }
        .nav a:hover { text-decoration: underline; }
        .btn { display: inline-block; padding: 0.5rem 1rem; border-radius: 6px; border: none; cursor: pointer; font-size: 0.9rem; text-decoration: none; }
        .btn-primary { background: #2563eb; color: #fff; }
        .btn-primary:hover { background: #1d4ed8; }
        .btn-success { background: #16a34a; color: #fff; }
        .btn-success:hover { background: #15803d; }
        .btn-secondary { background: #6b7280; color: #fff; }
        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; margin-bottom: 0.25rem; font-weight: 500; }
        input[type="text"], input[type="password"] { width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 6px; }
        .alert { padding: 0.75rem; border-radius: 6px; margin-bottom: 1rem; }
        .alert-error { background: #fef2f2; color: #b91c1c; }
        .alert-success { background: #f0fdf4; color: #166534; }
        .task-completed { color: #9ca3af; text-decoration: line-through; }
        .advice-card { background: #fff; border-radius: 14px; overflow: hidden; box-shadow: 0 4px 16px rgba(37,99,235,0.13); }
        .advice-card .advice-header { background: linear-gradient(135deg, #38bdf8 0%, #818cf8 100%); padding: 1.25rem 1.5rem 1.1rem; }
        .advice-card .advice-task { font-size: 1.2rem; font-weight: 800; color: #fff; letter-spacing: 0.01em; text-shadow: 0 1px 4px rgba(0,0,0,0.15); }
        .advice-card .advice-due { font-size: 0.82rem; color: rgba(255,255,255,0.9); margin-top: 0.3rem; }
        .advice-card .advice-body { padding: 1.25rem 1.5rem; }
        .advice-card .advice-text { font-size: 1.05rem; font-weight: 600; line-height: 1.8; color: #1e293b; background: linear-gradient(135deg, #f0f9ff 0%, #eef2ff 100%); border-left: 4px solid #38bdf8; border-radius: 8px; padding: 1rem 1.1rem; white-space: pre-wrap; }

        /* ローディングオーバーレイ */
        #loading-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(3px);
            z-index: 9999;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 1.25rem;
        }
        #loading-overlay.active { display: flex; }
        .spinner {
            width: 52px;
            height: 52px;
            border: 5px solid #e0e7ff;
            border-top-color: #2563eb;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        .loading-text { font-size: 0.95rem; color: #2563eb; font-weight: 600; letter-spacing: 0.03em; }
    </style>
</head>
<body>
    <!-- ローディングオーバーレイ -->
    <div id="loading-overlay">
        <div class="spinner"></div>
        <p class="loading-text">AIがアドバイスを考えています…</p>
    </div>

    <div class="container">
        @if(session('auth_verified'))
        <nav class="nav">
            <a href="{{ route('dashboard') }}">ダッシュボード</a>
            <a href="{{ route('tasks.index') }}">タスク一覧</a>
            <form action="{{ route('auth.logout') }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" class="btn btn-secondary">ログアウト</button>
            </form>
        </nav>
        @endif
        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @yield('content')
    </div>

    <script>
        const overlay = document.getElementById('loading-overlay');
        const dashboardUrl = '{{ route('dashboard') }}';

        function showLoading(message) {
            document.querySelector('.loading-text').textContent = message || '読み込み中…';
            overlay.classList.add('active');
            document.querySelectorAll('a, button, input, select, textarea').forEach(el => {
                el.style.pointerEvents = 'none';
            });
        }

        // ダッシュボードへのリンクをクリックしたとき
        document.addEventListener('click', function (e) {
            const anchor = e.target.closest('a');
            if (anchor && anchor.href === dashboardUrl) {
                showLoading('AIがアドバイスを考えています…');
            }
        });

        // 認証フォーム送信時
        document.addEventListener('submit', function (e) {
            const form = e.target;
            if (form.action === '{{ route('auth.login') }}') {
                showLoading('認証しています…');
            }
        });

        // ダッシュボードページ自体をリロード・直接アクセスしたとき
        if (window.location.pathname === new URL(dashboardUrl).pathname) {
            // ページ読み込み開始時にオーバーレイを表示
            overlay.classList.add('active');
            // DOM（コンテンツ）が表示できたら非表示にする
            window.addEventListener('pageshow', function () {
                overlay.classList.remove('active');
            });
            document.addEventListener('DOMContentLoaded', function () {
                overlay.classList.remove('active');
            });
        }

        // ブラウザの戻るボタン等でキャッシュから復帰した場合もオーバーレイを消す
        window.addEventListener('pageshow', function (e) {
            if (e.persisted) {
                overlay.classList.remove('active');
            }
        });
    </script>
</body>
</html>
