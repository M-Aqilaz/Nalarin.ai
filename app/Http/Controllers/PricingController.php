<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class PricingController extends Controller
{
    public function __invoke(): View
    {
        return view('pages.public.pricing');
    }
}
