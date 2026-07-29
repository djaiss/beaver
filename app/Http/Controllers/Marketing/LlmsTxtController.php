<?php

declare(strict_types=1);

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Services\LlmsTxt;
use Illuminate\Http\Response;

class LlmsTxtController extends Controller
{
    public function __construct(
        private LlmsTxt $llmsTxt,
    ) {}

    /**
     * llms.txt (see https://llmstxt.org): a plain Markdown index of the public
     * site for an assistant to read, the same way robots.txt and sitemap.xml
     * are one file for the whole host rather than one per language prefix.
     */
    public function index(): Response
    {
        return response($this->llmsTxt->content(), 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
        ]);
    }
}
