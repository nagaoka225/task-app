<?php

namespace App\Http\Controllers;

use App\Services\SupabaseService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class TaskController extends Controller
{
    public function __construct(
        private SupabaseService $supabase
    ) {}

    public function index(): View
    {
        $tasks = $this->supabase->getAllTasksOrderById();
        return view('tasks.index', ['tasks' => $tasks]);
    }

    public function complete(int $id): RedirectResponse
    {
        if ($this->supabase->completeTask($id)) {
            return redirect()->route('tasks.index')->with('success', 'タスクを完了にしました。');
        }
        return redirect()->route('tasks.index')->with('error', '更新に失敗しました。');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'task_content' => 'required|string|max:1000',
            'due_text'     => ['required', 'regex:/^\d{4}\/\d{1,2}\/\d{1,2}$/'],
        ], [
            'due_text.required' => '予定日を入力してください。',
            'due_text.regex'    => '予定日は YYYY/M/D 形式で入力してください（例: 2026/3/10 または 2026/03/10）。',
        ]);
        if ($this->supabase->createTask(
            $request->input('task_content'),
            $request->input('due_text')
        )) {
            return redirect()->route('tasks.index')->with('success', 'タスクを登録しました。');
        }
        return redirect()->route('tasks.index')->with('error', '登録に失敗しました。');
    }
}
