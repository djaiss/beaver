<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Vault\Adminland;

use App\Enums\PermissionEnum;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminlandController extends Controller
{
    public function index(Request $request): View
    {
        $member = $request->attributes->get('member');
        if ($member->role !== PermissionEnum::Owner->value) {
            abort(403);
        }

        return view('app.vault.adminland.index');
    }
}
