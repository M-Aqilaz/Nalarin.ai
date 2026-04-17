<?php

namespace App\Http\Controllers;

use App\Models\FeatureUsage;
use Illuminate\Http\Request;

class FeatureUsageController extends Controller
{
    public function track(Request $request)
    {
        $request->validate([
            'feature_name' => 'required|string'
        ]);

        $usage = FeatureUsage::firstOrCreate(
            ['feature_name' => $request->feature_name],
            ['click_count' => 0]
        );

        $usage->increment('click_count');

        return response()->json([
            'success' => true,
            'click_count' => $usage->click_count
        ]);
    }
}
