<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FeatureUsage;

class FeatureUsageController extends Controller
{
    public function track(Request $request)
    {
        $request->validate([
            'feature_name' => 'required|string|max:255',
        ]);

        $feature = FeatureUsage::firstOrCreate(
            ['feature_name' => $request->feature_name],
            ['click_count' => 0]
        );

        $feature->increment('click_count');

        return response()->json(['status' => 'success', 'message' => 'Tracked successfully', 'click_count' => $feature->click_count]);
    }
}
