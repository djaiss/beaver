<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Settings;

use App\Http\Controllers\Controller;
use App\ViewModels\Settings\SecurityIndexViewModel;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SecurityController extends Controller
{
    public function index(Request $request): View
    {
        $view = new SecurityIndexViewModel(
            user: $request->user(),
        );

        return view('app.settings.security.index', [
            'view' => $view,
        ]);
    }
}
