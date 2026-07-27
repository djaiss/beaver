<?php

declare(strict_types=1);

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TermsController extends Controller
{
    /**
     * The terms of use. The text is legal copy that has to say the same thing
     * to everybody, so it is written once in English and rendered from its
     * Markdown file rather than translated.
     */
    public function index(): View
    {
        return view('marketing.legal', [
            'title' => 'Terms of Use',
            'content' => Str::markdown((string) file_get_contents(resource_path('markdown/legal/terms.md'))),
        ]);
    }
}
