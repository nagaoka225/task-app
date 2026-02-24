<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class OpenAiService
{
    private string $apiKey;
    private string $model = 'gpt-4.1-mini';
    private string $url = 'https://api.openai.com/v1/responses';

    public function __construct()
    {
        $this->apiKey = config('services.openai.key');
    }

    public function getAdviceForTask(array $task): ?string
    {
        $taskContent = $task['task_content'];
        $dueText     = $task['due_text'];

        $prompt = <<<EOL
あなたは優しくてフレンドリーなアシスタントです。
励ますような、話しやすい口調で答えてください。

今日の日付: {$this->today()}

次のタスクについてアドバイスをください：
タスク名：{$taskContent}
予定日：{$dueText}

条件:
・このタスクをクリアするために何をすべきか、100文字前後でフレンドリーに答えてください。

必ず以下の形式で出力してください（ラベル名を変えず、コロンの後に値を入れること）：
タスク名：{$taskContent}
予定日：{$dueText}
アドバイス：[アドバイス本文]
EOL;

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->apiKey,
        ])->timeout(60)->post($this->url, [
            'model' => $this->model,
            'input' => $prompt,
        ]);

        if (!$response->successful()) {
            return null;
        }
        $data = $response->json();
        return $data['output'][0]['content'][0]['text'] ?? null;
    }

    private function today(): string
    {
        return now()->timezone('Asia/Tokyo')->format('Y年m月d日');
    }
}
