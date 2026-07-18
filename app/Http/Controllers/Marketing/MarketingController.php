<?php

declare(strict_types=1);

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Services\MarketingHomepage;
use Illuminate\View\View;

class MarketingController extends Controller
{
    public function index(MarketingHomepage $homepage): View
    {
        return view('marketing.index', $homepage->all());
    }
}
