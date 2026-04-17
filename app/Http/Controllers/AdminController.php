<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FeatureUsage;

class AdminController extends Controller
{
    public function index()
    {
        $featureUsages = FeatureUsage::orderBy('click_count', 'desc')->get();
        return view('Admin.adminDashboard', compact('featureUsages'));
    }
}
