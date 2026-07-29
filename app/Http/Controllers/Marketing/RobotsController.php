<?php

declare(strict_types=1);

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

class RobotsController extends Controller
{
    /**
     * robots.txt, which is answered whether or not the public site is switched
     * on. An instance that keeps it off is somebody's private catalogue behind a
     * login, and it still wants to say so: the redirect to the login page the
     * marketing gate would answer with tells a crawler nothing.
     */
    public function index(): Response
    {
        if (! config('marketing.show')) {
            return response(
                "User-agent: *\nDisallow: /\n",
                headers: ['Content-Type' => 'text/plain; charset=UTF-8'],
            );
        }

        $lines = [
            'User-agent: *',
            'Allow: /',
            '',
            'Sitemap: '.route('marketing.sitemap.index'),
            '',
        ];

        return response(
            implode("\n", $lines),
            headers: ['Content-Type' => 'text/plain; charset=UTF-8'],
        );
    }
}
