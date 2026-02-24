@extends('layouts.app')

@section('title', '認証')

@section('content')
<div class="card">
    <h1>認証コードを入力</h1>
    <p>認証コードを知っている方のみアクセスできます。</p>
    <form method="POST" action="{{ route('auth.login') }}">
        @csrf
        <div class="form-group">
            <label for="auth_code">認証コード</label>
            <input type="password" id="auth_code" name="auth_code" required autofocus>
        </div>
        <button type="submit" class="btn btn-primary">送信</button>
    </form>
</div>
@endsection
