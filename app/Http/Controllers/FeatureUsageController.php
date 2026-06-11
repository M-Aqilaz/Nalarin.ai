<?php

namespace App\Http\Controllers;

use App\Models\FeatureUsage;
use App\Services\Analytics\AnalyticsTracker;
use Illuminate\Http\Request;

class FeatureUsageController extends Controller
{
    public function track(Request $request, AnalyticsTracker $tracker)
    {
        $request->validate([
            'feature_name' => 'required|string'
        ]);

        $tracker->trackFeatureByName(
            null,
            $request->feature_name,
            'click',
            [
                'referer' => $request->headers->get('referer'),
                'user_agent' => $request->userAgent(),
            ],
            $request,
        );

        $usage = FeatureUsage::where('feature_name', $request->feature_name)->first();

        return response()->json([
            'success' => true,
            'click_count' => $usage?->click_count ?? 0
        ]);
    }
}
