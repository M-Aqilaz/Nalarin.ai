<?php

namespace App\Http\Controllers;

use App\Models\FeatureUsage;
use App\Models\Material;
use App\Models\User;

class AdminController extends Controller
{
    public function index()
    {
        $featureUsages = FeatureUsage::orderBy('click_count', 'desc')->get();
        $totalUsers = User::count();
        $totalAiRequests = (int) FeatureUsage::sum('click_count');

        $totalUsers = User::count();
        $totalAiRequests = (int) FeatureUsage::sum('click_count');

        $stats = [
            'total_users' => $totalUsers,
            'active_users' => User::where('is_active', true)->count(),
            'total_documents' => Material::count(),
            'total_ai_requests' => $totalAiRequests,
        ];

        return view('pages.admin.dashboard', compact('featureUsages', 'stats'));
    }

    public function monitoringAi()
    {
        $totalUsers = User::count();
        $totalAiRequests = (int) FeatureUsage::sum('click_count');

        $aiStats = [
            'total_requests' => $totalAiRequests,
            'errors' => 24, // Mock data
            'avg_response_time' => '1.5s', // Mock data
            'usage_per_user' => $totalUsers > 0 ? round($totalAiRequests / $totalUsers, 1) : 0,
        ];

        return view('pages.admin.monitoring-ai', compact('aiStats'));
    }

    public function statistikPembelajaran()
    {
        $learningStats = [
            'avg_quiz_score' => 82, // Mock percentage
            'most_used_feature' => FeatureUsage::orderBy('click_count', 'desc')->first()?->feature_name ?? 'N/A',
            'learning_activity' => 345, // Mock data: total hours or sessions
            'overall_score' => 88, // Mock data
        ];

        return view('pages.admin.statistik-pembelajaran', compact('learningStats'));
    }
}
