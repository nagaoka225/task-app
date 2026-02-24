<?php

// ========================================
// 1. 環境変数読み込み
// ========================================
$api_key_openai = getenv('OPENAI_API_KEY');
$project_url    = getenv('SUPABASE_URL');
$api_key_sb     = getenv('SUPABASE_PUBLIC_KEY');

if (!$api_key_openai) die("OPENAI_API_KEY が未設定です\n");
if (!$project_url)    die("SUPABASE_URL が未設定です\n");
if (!$api_key_sb)     die("SUPABASE_PUBLIC_KEY が未設定です\n");

$openai_url = "https://api.openai.com/v1/responses";
$model      = "gpt-4.1-mini";


// ========================================
// 2. Supabase から未完了タスク取得
// ========================================
$url = $project_url . "/rest/v1/tb_tasks?select=task_content,due_text&status=eq.0";

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_HTTPHEADER => [
        "apikey: $api_key_sb",
        "Authorization: Bearer $api_key_sb",
        "Content-Type: application/json",
    ],
    CURLOPT_RETURNTRANSFER => true,
]);
$response = curl_exec($ch);
$tasks = json_decode($response, true);

if (!$tasks || !is_array($tasks)) {
    die("タスクの取得に失敗:\n$response\n");
}


// ========================================
// 3. AI に送る形へ整形
// ========================================
$task_lines = [];
foreach ($tasks as $t) {
    $task_lines[] = "完了予定日: {$t['due_text']} / タスク: {$t['task_content']}";
}
$task_text = implode("\n", $task_lines);


// ========================================
// 4. AI 用プロンプト
// ========================================
$prompt = <<<EOL
あなたは優しくてフレンドリーなアシスタントです。
励ますような、話しやすい口調で答えてください。

次の未完了タスク一覧があります：

$task_text

条件:
1. 直近で取り組むべきタスクを1つ選んでください。
2. そのタスクの「予定日」を必ず表示してください。
3. そのタスクをクリアするために何をすべきか、100文字前後でフレンドリーに答えてください。

出力フォーマット:
・タスク名
・予定日（必ず含める）
・アドバイス（100文字以内）
EOL;


// ========================================
// 5. OpenAI API 呼び出し
// ========================================
$postData = [
    "model" => $model,
    "input" => $prompt
];

$ch = curl_init($openai_url);
curl_setopt_array($ch, [
    CURLOPT_HTTPHEADER => [
        "Content-Type: application/json",
        "Authorization: Bearer " . $api_key_openai
    ],
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($postData),
    CURLOPT_RETURNTRANSFER => true,
]);

$ai_response = curl_exec($ch);
$data = json_decode($ai_response, true);


// ========================================
// 6. AI回答出力
// ========================================
if (isset($data["output"][0]["content"][0]["text"])) {
    echo "\n===== 直近のタスク =====\n";
    echo $data["output"][0]["content"][0]["text"] . "\n";
} else {
    echo "AIレスポンス異常:\n";
    print_r($data);
}
