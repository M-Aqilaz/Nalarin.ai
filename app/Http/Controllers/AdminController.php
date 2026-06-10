<?php

namespace App\Http\Controllers;

use App\Models\AiRequest;
use App\Models\AiSummary;
use App\Models\ChatMessage;
use App\Models\FeatureEvent;
use App\Models\FlashcardDeck;
use App\Models\Material;
use App\Models\QuizAttempt;
use App\Models\QuizQuestion;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $analyticsFilter = $this->analyticsFilter($request);
        $featureUsages = $this->featureUsageSummary($analyticsFilter);
        $totalAiRequests = $this->aiRequestCount($analyticsFilter);

        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
            'total_documents' => Material::count(),
            'total_ai_requests' => $totalAiRequests,
        ];

        $featureUsageChart = [
            'labels' => $featureUsages->pluck('feature_name')->values(),
            'data' => $featureUsages->pluck('usage_count')->map(fn ($count) => (int) $count)->values(),
        ];
        $recentActivities = $this->recentActivities($analyticsFilter);

        return view('pages.admin.dashboard', compact('featureUsages', 'featureUsageChart', 'recentActivities', 'stats', 'analyticsFilter'));
    }

    public function monitoringAi(Request $request)
    {
        $analyticsFilter = $this->analyticsFilter($request);
        $totalUsers = max(1, User::count());
        $totalAiRequests = $this->aiRequestCount($analyticsFilter);

        $aiStats = [
            'total_requests' => $totalAiRequests,
            'errors' => $this->aiErrorCount($analyticsFilter),
            'avg_response_time' => $this->averageAiResponseTime($analyticsFilter),
            'usage_per_user' => round($totalAiRequests / $totalUsers, 1),
        ];
        $aiTrendChart = $this->aiTrendChart($analyticsFilter);

        return view('pages.admin.monitoring-ai', compact('aiStats', 'aiTrendChart', 'analyticsFilter'));
    }

    public function statistikPembelajaran(Request $request)
    {
        $analyticsFilter = $this->analyticsFilter($request);
        $quizScore = $this->averageQuizScore($analyticsFilter);
        $processedMaterials = Material::where('status', 'processed')->count();
        $totalMaterials = Material::count();
        $materialReadiness = $totalMaterials > 0 ? round(($processedMaterials / $totalMaterials) * 100) : 0;
        $activeUserRatio = User::count() > 0 ? round((User::where('is_active', true)->count() / User::count()) * 100) : 0;
        $activityTotal = $this->learningActivityTotal($analyticsFilter);
        $activityScore = min(100, $activityTotal * 6);

        $learningStats = [
            'avg_quiz_score' => $quizScore,
            'most_used_feature' => $this->featureUsageSummary($analyticsFilter)->first()?->feature_name ?? 'N/A',
            'learning_activity' => $activityTotal,
            'overall_score' => (int) round(($materialReadiness * 0.25) + ($quizScore * 0.3) + ($activeUserRatio * 0.2) + ($activityScore * 0.25)),
        ];
        $learningActivityChart = $this->learningActivityChart($analyticsFilter);

        return view('pages.admin.statistik-pembelajaran', compact('learningStats', 'learningActivityChart', 'analyticsFilter'));
    }

    private function analyticsFilter(Request $request): array
    {
        $range = (string) $request->query('range', '7d');
        $allowedRanges = ['today', '7d', '30d', 'all', 'custom'];

        if (! in_array($range, $allowedRanges, true)) {
            $range = '7d';
        }

        $now = now();
        $start = null;
        $end = null;
        $label = '7 hari terakhir';

        if ($range === 'today') {
            $start = $now->copy()->startOfDay();
            $end = $now->copy()->endOfDay();
            $label = 'Hari ini';
        } elseif ($range === '7d') {
            $start = $now->copy()->subDays(6)->startOfDay();
            $end = $now->copy()->endOfDay();
            $label = '7 hari terakhir';
        } elseif ($range === '30d') {
            $start = $now->copy()->subDays(29)->startOfDay();
            $end = $now->copy()->endOfDay();
            $label = '30 hari terakhir';
        } elseif ($range === 'custom') {
            [$start, $end] = $this->customDateRange($request);
            $label = $start && $end
                ? $start->format('d M Y').' - '.$end->format('d M Y')
                : 'Rentang custom';
        } else {
            $label = 'Semua waktu';
        }

        return [
            'range' => $range,
            'start' => $start,
            'end' => $end,
            'start_date' => $start?->toDateString(),
            'end_date' => $end?->toDateString(),
            'label' => $label,
        ];
    }

    private function customDateRange(Request $request): array
    {
        try {
            $start = $request->filled('start_date')
                ? Carbon::parse((string) $request->query('start_date'))->startOfDay()
                : null;
            $end = $request->filled('end_date')
                ? Carbon::parse((string) $request->query('end_date'))->endOfDay()
                : null;
        } catch (\Throwable) {
            return [null, null];
        }

        if ($start && $end && $start->greaterThan($end)) {
            return [$end->copy()->startOfDay(), $start->copy()->endOfDay()];
        }

        return [$start, $end];
    }

    private function applyDateFilter(Builder $query, array $filter, string $column = 'created_at'): Builder
    {
        if ($filter['start']) {
            $query->where($column, '>=', $filter['start']);
        }

        if ($filter['end']) {
            $query->where($column, '<=', $filter['end']);
        }

        return $query;
    }

    private function featureUsageSummary(array $filter): Collection
    {
        return $this->applyDateFilter(
            FeatureEvent::query()
                ->selectRaw('feature_key, feature_name, COUNT(*) as usage_count')
                ->groupBy('feature_key', 'feature_name')
                ->orderByDesc('usage_count'),
            $filter,
            'occurred_at',
        )->get();
    }

    private function aiRequestCount(array $filter): int
    {
        return $this->applyDateFilter(AiRequest::query(), $filter)->count();
    }

    private function aiErrorCount(array $filter): int
    {
        return $this->applyDateFilter(AiRequest::query(), $filter)
            ->where('status', AiRequest::STATUS_FAILED)
            ->count();
    }

    private function averageAiResponseTime(array $filter): string
    {
        $averageMs = (int) round((float) $this->applyDateFilter(AiRequest::query(), $filter)
            ->where('status', AiRequest::STATUS_SUCCESS)
            ->whereNotNull('latency_ms')
            ->avg('latency_ms'));

        if ($averageMs <= 0) {
            return '0s';
        }

        if ($averageMs >= 60000) {
            return round($averageMs / 60000, 1).'m';
        }

        return round($averageMs / 1000, 1).'s';
    }

    private function recentActivities(array $filter): Collection
    {
        return collect()
            ->merge($this->applyDateFilter(User::query()->latest(), $filter)->take(4)->get()->map(fn (User $user) => [
                'title' => $user->name,
                'description' => 'User baru terdaftar sebagai '.$user->role,
                'time' => $user->created_at,
                'badge' => 'User',
            ]))
            ->merge($this->applyDateFilter(Material::with('user')->latest(), $filter)->take(4)->get()->map(fn (Material $material) => [
                'title' => $material->title,
                'description' => 'Materi diunggah oleh '.($material->user?->name ?? 'User'),
                'time' => $material->created_at,
                'badge' => 'Materi',
            ]))
            ->merge($this->applyDateFilter(AiSummary::with('user')->latest(), $filter)->take(4)->get()->map(fn (AiSummary $summary) => [
                'title' => $summary->title,
                'description' => 'Ringkasan dibuat untuk '.($summary->user?->name ?? 'User'),
                'time' => $summary->created_at,
                'badge' => 'AI',
            ]))
            ->merge($this->applyDateFilter(FeatureEvent::with('user')->latest('occurred_at'), $filter, 'occurred_at')->take(4)->get()->map(fn (FeatureEvent $event) => [
                'title' => $event->feature_name,
                'description' => ucfirst(str_replace('_', ' ', $event->action)).' oleh '.($event->user?->name ?? 'pengunjung'),
                'time' => $event->occurred_at,
                'badge' => 'Event',
            ]))
            ->sortByDesc('time')
            ->take(6)
            ->values();
    }

    private function aiTrendChart(array $filter): array
    {
        $days = $this->chartDays($filter);
        $events = $this->applyDateFilter(AiRequest::query(), $filter)
            ->where('created_at', '>=', $days->first())
            ->get(['created_at'])
            ->groupBy(fn (AiRequest $item) => $item->created_at->format('Y-m-d'));

        return [
            'labels' => $days->map(fn (Carbon $day) => $day->format('d M'))->values(),
            'data' => $days->map(fn (Carbon $day) => $events->get($day->format('Y-m-d'), collect())->count())->values(),
        ];
    }

    private function chartDays(array $filter): Collection
    {
        $end = ($filter['end'] ?? now())->copy()->startOfDay();
        $start = $filter['start']
            ? $filter['start']->copy()->startOfDay()
            : $end->copy()->subDays(6);

        if ($start->diffInDays($end) > 29) {
            $start = $end->copy()->subDays(29);
        }

        return collect(range(0, max(0, $start->diffInDays($end))))
            ->map(fn (int $offset) => $start->copy()->addDays($offset));
    }

    private function averageQuizScore(array $filter): int
    {
        $average = $this->applyDateFilter(
            QuizAttempt::query()->where('status', QuizAttempt::STATUS_COMPLETED),
            $filter,
            'completed_at',
        )->avg('percentage');

        return (int) round((float) $average);
    }

    private function learningActivityTotal(array $filter): int
    {
        return $this->applyDateFilter(Material::query(), $filter)->count()
            + $this->applyDateFilter(AiSummary::query(), $filter)->count()
            + $this->applyDateFilter(QuizAttempt::query()->where('status', QuizAttempt::STATUS_COMPLETED), $filter, 'completed_at')->count()
            + $this->applyDateFilter(QuizQuestion::query(), $filter)->count()
            + $this->applyDateFilter(FlashcardDeck::query(), $filter)->count()
            + $this->applyDateFilter(ChatMessage::query(), $filter)->count()
            + $this->applyDateFilter(FeatureEvent::query(), $filter, 'occurred_at')->count();
    }

    private function learningActivityChart(array $filter): array
    {
        return [
            'labels' => ['Materi', 'Ringkasan', 'Quiz Selesai', 'Soal', 'Flashcard Deck', 'Chat', 'Event Fitur'],
            'data' => [
                $this->applyDateFilter(Material::query(), $filter)->count(),
                $this->applyDateFilter(AiSummary::query(), $filter)->count(),
                $this->applyDateFilter(QuizAttempt::query()->where('status', QuizAttempt::STATUS_COMPLETED), $filter, 'completed_at')->count(),
                $this->applyDateFilter(QuizQuestion::query(), $filter)->count(),
                $this->applyDateFilter(FlashcardDeck::query(), $filter)->count(),
                $this->applyDateFilter(ChatMessage::query(), $filter)->count(),
                $this->applyDateFilter(FeatureEvent::query(), $filter, 'occurred_at')->count(),
            ],
        ];
    }
}
