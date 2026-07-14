<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Settings;

use App\Actions\DestroyUser;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        return view('app.settings.user.index');
    }

    public function destroy(Request $request): RedirectResponse
    {
        new DestroyUser(
            user: $request->user(),
            reason: $request->input('feedback'),
        )->execute();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return to_route('login');
    }
}
