<?php

namespace App\Http\Controllers;

use App\Services\OpenAiService;
use App\Services\SupabaseService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private SupabaseService $supabase,
        private OpenAiService $openAi
    ) {}

    public function index(): View
    {
        $tasks = $this->supabase->getIncompleteTasks();
        $nearest = $this->pickNearest($tasks);
        $advice = $nearest ? $this->openAi->getAdviceForTask($nearest) : null;
        return view('dashboard', [
            'tasks'   => $tasks,
            'nearest' => $nearest,
            'advice'  => $advice,
        ]);
    }

    private function pickNearest(array $tasks): ?array
    {
        if (empty($tasks)) {
            return null;
        }
        usort($tasks, function (array $a, array $b) {
            $ta = $this->parseDate($a['due_text']);
            $tb = $this->parseDate($b['due_text']);
            if ($ta === null && $tb === null) return 0;
            if ($ta === null) return 1;
            if ($tb === null) return -1;
            return $ta <=> $tb;
        });
        return $tasks[0];
    }

    private function parseDate(string $text): ?int
    {
        // YYYY/MM/DD 形式のみ対応
        $ts = strtotime(trim($text));
        return $ts !== false ? $ts : null;
    }
}
