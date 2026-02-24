<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SupabaseService
{
    private string $url;
    private string $apiKey;

    public function __construct()
    {
        $this->url = rtrim(config('services.supabase.url'), '/');
        $this->apiKey = config('services.supabase.key');
    }

    public function getIncompleteTasks(): array
    {
        $response = Http::withHeaders([
            'apikey' => $this->apiKey,
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->get("{$this->url}/rest/v1/tb_tasks", [
            'select' => 'id,task_content,due_text,status',
            'status' => 'eq.0',
            'order' => 'id.asc',
        ]);
        if (!$response->successful()) {
            return [];
        }
        return $response->json() ?? [];
    }

    public function getAllTasksOrderById(): array
    {
        $response = Http::withHeaders([
            'apikey' => $this->apiKey,
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->get("{$this->url}/rest/v1/tb_tasks", [
            'select' => 'id,task_content,due_text,status',
            'order' => 'id.asc',
        ]);
        if (!$response->successful()) {
            return [];
        }
        return $response->json() ?? [];
    }

    public function completeTask(int $id): bool
    {
        $response = Http::withHeaders([
            'apikey' => $this->apiKey,
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
            'Prefer' => 'return=minimal',
        ])->patch("{$this->url}/rest/v1/tb_tasks?id=eq.{$id}", [
            'status' => 1,
        ]);
        return $response->successful();
    }

    public function createTask(string $taskContent, string $dueText): bool
    {
        $response = Http::withHeaders([
            'apikey' => $this->apiKey,
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
            'Prefer' => 'return=minimal',
        ])->post("{$this->url}/rest/v1/tb_tasks", [
            'task_content' => $taskContent,
            'due_text' => $dueText,
            'status' => 0,
        ]);
        return $response->successful();
    }
}
