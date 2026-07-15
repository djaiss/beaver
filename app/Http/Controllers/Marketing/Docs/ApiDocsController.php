<?php

declare(strict_types=1);

namespace App\Http\Controllers\Marketing\Docs;

use App\Http\Controllers\Controller;
use App\Services\ApiDocumentation;
use Illuminate\View\View;

class ApiDocsController extends Controller
{
    public function index(ApiDocumentation $documentation): View
    {
        return view('marketing.docs.index', [
            'navigation' => $documentation->navigation(),
            'sections' => $documentation->sections(),
        ]);
    }
}
