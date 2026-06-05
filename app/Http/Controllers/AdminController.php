<?php

namespace App\Http\Controllers;

use App\Models\AiSummary;
use App\Models\ChatMessage;
use App\Models\ChatThread;
use App\Models\FeatureUsage;
use App\Models\FlashcardDeck;
use App\Models\Material;
use App\Models\QuizQuestion;
use App\Models\QuizSet;
use App\Models\User;
use Illuminate\Support\Collection;

class AdminController extends Controller
{
    public function index()
    {
        $this->ensureFeatureUsageData();

        $featureUsages = FeatureUsage::orderBy('click_count', 'desc')->get();
        $totalUsers = User::count();
        $totalAiRequests = $this->aiRequestCount();

        $stats = [
            'total_users' => $totalUsers,
            'active_users' => User::where('is_active', true)->count(),
            'total_documents' => Material::count(),
            'total_ai_requests' => $totalAiRequests,
        ];

        $featureUsageChart = [
            'labels' => $featureUsages->pluck('feature_name')->values(),
            'data' => $featureUsages->pluck('click_count')->map(fn ($count) => (int) $count)->values(),
        ];
        $recentActivities = $this->recentActivities();

        return view('pages.admin.dashboard', compact('featureUsages', 'featureUsageChart', 'recentActivities', 'stats'));
    }

    public function monitoringAi()
    {
        $this->ensureFeatureUsageData();

        $totalUsers = User::count();
        $totalAiRequests = $this->aiRequestCount();

        $aiStats = [
            'total_requests' => $totalAiRequests,
            'errors' => $this->aiErrorCount(),
            'avg_response_time' => $this->averageAiResponseTime(),
            'usage_per_user' => $totalUsers > 0 ? round($totalAiRequests / $totalUsers, 1) : 0,
        ];
        $aiTrendChart = $this->aiTrendChart();

        return view('pages.admin.monitoring-ai', compact('aiStats', 'aiTrendChart'));
    }

    public function statistikPembelajaran()
    {
        $this->ensureFeatureUsageData();

        $quizReadiness = $this->quizReadinessScore();
        $processedMaterials = Material::where('status', 'processed')->count();
        $totalMaterials = Material::count();
        $materialReadiness = $totalMaterials > 0 ? round(($processedMaterials / $totalMaterials) * 100) : 0;
        $activeUserRatio = User::count() > 0 ? round((User::where('is_active', true)->count() / User::count()) * 100) : 0;
        $activityTotal = $this->learningActivityTotal();
        $activityScore = min(100, $activityTotal * 6);

        $learningStats = [
            'avg_quiz_score' => $quizReadiness,
            'most_used_feature' => FeatureUsage::orderBy('click_count', 'desc')->first()?->feature_name ?? 'N/A',
            'learning_activity' => $activityTotal,
            'overall_score' => (int) round(($materialReadiness * 0.25) + ($quizReadiness * 0.3) + ($activeUserRatio * 0.2) + ($activityScore * 0.25)),
        ];
        $learningActivityChart = $this->learningActivityChart();

        return view('pages.admin.statistik-pembelajaran', compact('learningStats', 'learningActivityChart'));
    }

    private function ensureFeatureUsageData(): void
    {
        if (FeatureUsage::exists()) {
            return;
        }

        foreach ([
            'Unggah Materi' => 18,
            'Ringkasan Otomatis' => 14,
            'AI Tutor Khusus' => 12,
            'Smart Flashcards' => 11,
            'Latihan Kuis' => 10,
            'Group Chat Kelas' => 8,
            'Study Matching' => 6,
        ] as $featureName => $clickCount) {
            FeatureUsage::create([
                'feature_name' => $featureName,
                'click_count' => $clickCount,
            ]);
        }
    }

    private function aiRequestCount(): int
    {
        return AiSummary::count()
            + QuizSet::count()
            + FlashcardDeck::count()
            + ChatMessage::where('role', 'assistant')->count();
    }

    private function aiErrorCount(): int
    {
        return ChatThread::where('ai_status', 'failed')->count()
            + Material::whereIn('status', ['failed', 'error'])->count()
            + Material::where('ocr_status', 'failed')->count();
    }

    private function averageAiResponseTime(): string
    {
        $threads = ChatThread::query()
            ->whereHas('messages', fn ($query) => $query->where('role', 'assistant'))
            ->get(['created_at', 'updated_at']);

        if ($threads->isEmpty()) {
            return '0s';
        }

        $averageSeconds = (int) round($threads->avg(fn (ChatThread $thread) => $thread->created_at->diffInSeconds($thread->updated_at, true)));

        return $averageSeconds >= 60
            ? round($averageSeconds / 60, 1) . 'm'
            : $averageSeconds . 's';
    }

    private function recentActivities(): Collection
    {
        return collect()
            ->merge(User::latest()->take(4)->get()->map(fn (User $user) => [
                'title' => $user->name,
                'description' => 'User baru terdaftar sebagai ' . $user->role,
                'time' => $user->created_at,
                'badge' => 'User',
            ]))
            ->merge(Material::with('user')->latest()->take(4)->get()->map(fn (Material $material) => [
                'title' => $material->title,
                'description' => 'Materi diunggah oleh ' . ($material->user?->name ?? 'User'),
                'time' => $material->created_at,
                'badge' => 'Materi',
            ]))
            ->merge(AiSummary::with('user')->latest()->take(4)->get()->map(fn (AiSummary $summary) => [
                'title' => $summary->title,
                'description' => 'Ringkasan dibuat untuk ' . ($summary->user?->name ?? 'User'),
                'time' => $summary->created_at,
                'badge' => 'AI',
            ]))
            ->sortByDesc('time')
            ->take(6)
            ->values();
    }

    private function aiTrendChart(): array
    {
        $days = collect(range(6, 1))->map(fn ($daysAgo) => now()->subDays($daysAgo)->startOfDay())->push(now()->startOfDay());
        $events = collect()
            ->merge(AiSummary::where('created_at', '>=', $days->first())->get(['created_at']))
            ->merge(QuizSet::where('created_at', '>=', $days->first())->get(['created_at']))
            ->merge(FlashcardDeck::where('created_at', '>=', $days->first())->get(['created_at']))
            ->merge(ChatMessage::where('role', 'assistant')->where('created_at', '>=', $days->first())->get(['created_at']))
            ->groupBy(fn ($item) => $item->created_at->format('Y-m-d'));

        return [
            'labels' => $days->map(fn ($day) => $day->format('d M'))->values(),
            'data' => $days->map(fn ($day) => $events->get($day->format('Y-m-d'), collect())->count())->values(),
        ];
    }

    private function quizReadinessScore(): int
    {
        $quizSets = QuizSet::withCount('questions')->get();

        if ($quizSets->isEmpty()) {
            return 0;
        }

        return (int) round($quizSets->avg(fn (QuizSet $quizSet) => min(100, ($quizSet->questions_count / max(1, $quizSet->question_count ?: 5)) * 100)));
    }

    private function learningActivityTotal(): int
    {
        return Material::count()
            + AiSummary::count()
            + QuizSet::count()
            + QuizQuestion::count()
            + FlashcardDeck::count()
            + ChatMessage::count();
    }

    private function learningActivityChart(): array
    {
        return [
            'labels' => ['Materi', 'Ringkasan', 'Quiz', 'Soal', 'Flashcard Deck', 'Chat'],
            'data' => [
                Material::count(),
                AiSummary::count(),
                QuizSet::count(),
                QuizQuestion::count(),
                FlashcardDeck::count(),
                ChatMessage::count(),
            ],
        ];
    }
}
