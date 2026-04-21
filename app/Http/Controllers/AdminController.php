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
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
            'total_documents' => Material::count(),
            'total_ai_requests' => (int) FeatureUsage::sum('click_count'),
        ];

        return view('pages.admin.dashboard', compact('featureUsages', 'stats'));
    }
}
