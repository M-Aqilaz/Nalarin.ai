<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FeatureUsage;
use App\Models\User;
use App\Models\Document;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function index()
    {
        // Data for Chart
        $featureUsages = FeatureUsage::orderBy('click_count', 'desc')->get();
        
        // Monitoring Overview Stats
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('updated_at', '>=', Carbon::today())->count(),
            'total_documents' => Document::count(),
            'total_ai_requests' => FeatureUsage::sum('click_count')
        ];

        return view('Admin.adminDashboard', compact('featureUsages', 'stats'));
    }
}
