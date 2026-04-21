<?php

namespace App\Http\Controllers\Learning;

use App\Http\Controllers\Controller;
use App\Models\AiSummary;
use Illuminate\View\View;

class SummaryController extends Controller
{
    public function index(): View
    {
        $summaries = AiSummary::query()
            ->where('user_id', auth()->id())
            ->with(['material', 'user'])
            ->latest()
            ->get();

        return view('pages.user.summaries.index', compact('summaries'));
    }

    public function show(AiSummary $summary): View
    {
        abort_unless($summary->user_id === auth()->id(), 403);
        $summary->load(['material', 'user']);

        return view('pages.user.summaries.show', compact('summary'));
    }
}
