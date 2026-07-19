<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Instance;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class SupportController extends Controller
{
    /**
     * Beaver has no support tickets yet. The page exists so the section is
     * discoverable, and says as much.
     */
    public function index(): View
    {
        return view('app.instance.support.index');
    }
}
