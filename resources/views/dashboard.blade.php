@extends('layouts.app')

@section('title', 'ダッシュボード')

@section('content')
<h1>あなたが次にやるべきタスク</h1>

@if(!empty($tasks))
    @if($advice)
        @php
            $taskLine = '';
            $dueLine  = '';
            $advText  = '';
            foreach (explode("\n", $advice) as $line) {
                $line = trim($line);
                if (preg_match('/^タスク名[：:]\s*(.+)/u', $line, $m)) {
                    $taskLine = trim($m[1]);
                } elseif (preg_match('/^予定日[：:]\s*(.+)/u', $line, $m)) {
                    $dueLine = '予定日：' . trim($m[1]);
                } elseif (preg_match('/^アドバイス[：:]\s*(.+)/u', $line, $m)) {
                    $advText = trim($m[1]);
                }
            }
            // 万一パースできなかった場合はそのまま表示
            if ($taskLine === '' && $advText === '') {
                $advText = trim($advice);
            }
        @endphp
        <div class="advice-card">
            <div class="advice-header">
                <div class="advice-task">{{ $taskLine ?: '直近のタスク' }}</div>
                @if($dueLine)
                    <div class="advice-due">{{ $dueLine }}</div>
                @endif
            </div>
            <div class="advice-body">
                <div class="advice-text">{{ $advText ?: $advice }}</div>
            </div>
        </div>
    @else
        <div class="card">
            <p>AIからのアドバイスを取得できませんでした。</p>
        </div>
    @endif
@else
    <div class="card">
        <p>未完了のタスクはありません。タスク一覧から新規登録できます。</p>
    </div>
@endif

@endsection
