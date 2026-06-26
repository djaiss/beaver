<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Settings;

use App\Http\Controllers\Controller;
use App\Models\EmailSent;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailSentController extends Controller
{
    public function index(Request $request): View
    {
        $emails = EmailSent::query()
            ->where('user_id', $request->user()->id)
            ->latest('sent_at')
            ->cursorPaginate(10);

        return view('app.settings.emails.index', [
            'emails' => $emails,
        ]);
    }
}
