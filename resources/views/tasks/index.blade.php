@extends('layouts.app')

@section('title', 'タスク一覧')

@section('content')
<h1>タスク一覧</h1>

<div class="card">
    <h2>新規タスク登録</h2>
    <form method="POST" action="{{ route('tasks.store') }}">
        @csrf
        <div class="form-group">
            <label for="task_content">タスク</label>
            <input type="text" id="task_content" name="task_content" required maxlength="1000" placeholder="タスク内容" value="{{ old('task_content') }}">
            @error('task_content')
                <p style="color:#b91c1c; font-size:0.85rem; margin-top:0.25rem;">{{ $message }}</p>
            @enderror
        </div>
        <div class="form-group">
            <label for="due_text">予定日（YYYY/MM/DD）</label>
            <input type="text" id="due_text" name="due_text" required maxlength="10"
                   placeholder="例: 2026/03/25"
                   pattern="\d{4}/\d{1,2}/\d{1,2}"
                   title="YYYY/M/D 形式で入力してください（例: 2026/3/10 または 2026/03/10）"
                   value="{{ old('due_text') }}">
            @error('due_text')
                <p style="color:#b91c1c; font-size:0.85rem; margin-top:0.25rem;">{{ $message }}</p>
            @enderror
        </div>
        <button type="submit" class="btn btn-primary">登録</button>
    </form>
</div>

<div class="card">
    <h2>一覧（ID順）</h2>
    @if(empty($tasks))
        <p>タスクがありません。</p>
    @else
        <ul style="list-style: none; padding: 0;">
            @foreach($tasks as $task)
            <li style="padding: 0.5rem 0; border-bottom: 1px solid #eee; display: flex; align-items: center; gap: 1rem;">
                <span class="{{ $task['status'] == 1 ? 'task-completed' : '' }}">
                    #{{ $task['id'] }} 予定日: {{ $task['due_text'] }} — {{ $task['task_content'] }}
                </span>
                @if($task['status'] == 0)
                <form action="{{ route('tasks.complete', $task['id']) }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="btn btn-success">完了</button>
                </form>
                @endif
            </li>
            @endforeach
        </ul>
    @endif
</div>

<p><a href="{{ route('dashboard') }}" class="btn btn-secondary">ダッシュボードへ戻る</a></p>
@endsection
