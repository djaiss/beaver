<?php

declare(strict_types=1);

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PrivacyController extends Controller
{
    /**
     * The privacy policy. English only for the same reason as the terms of use:
     * it is legal copy, and a translation of it would be a second version of
     * something that has to read identically for everybody.
     */
    public function index(): View
    {
        return view('marketing.legal', [
            'title' => 'Privacy Policy',
            'content' => Str::markdown((string) file_get_contents(resource_path('markdown/legal/privacy.md'))),
        ]);
    }
}
