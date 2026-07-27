<?php

declare(strict_types=1);

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class MediaKitController extends Controller
{
    /**
     * The press kit. Like the terms and the privacy policy, the text is written
     * once in English and is not translated: it is boilerplate a journalist
     * copies verbatim, so a translated version would be a different quote. The
     * page says so at the top when the visitor is reading the site in another
     * language.
     */
    public function index(): View
    {
        return view('marketing.mediaKit');
    }
}
