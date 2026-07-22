<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleOversizedUpload
{
    /**
     * When an upload is larger than PHP's post_max_size, PHP discards the whole
     * body: no input, no files, and no CSRF token. Left alone that surfaces as a
     * confusing token mismatch or an empty form. Catch it first and send the user
     * back with a clear message instead.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $this->exceededPostMaxSize($request)) {
            return $next($request);
        }

        return back()->with('error', __('The upload was too large. Please choose a smaller file and try again.'));
    }

    private function exceededPostMaxSize(Request $request): bool
    {
        $contentLength = (int) $request->server('CONTENT_LENGTH', 0);

        if ($contentLength === 0) {
            return false;
        }

        $limit = $this->postMaxSizeInBytes();

        if ($limit === 0) {
            return false;
        }

        if ($contentLength <= $limit) {
            return false;
        }

        // A body over the limit that also arrived empty is the tell-tale sign PHP
        // dropped it, rather than a genuinely empty request we should let through.
        return $request->request->count() === 0 && count($request->allFiles()) === 0;
    }

    private function postMaxSizeInBytes(): int
    {
        $value = trim((string) ini_get('post_max_size'));

        if ($value === '') {
            return 0;
        }

        $unit = strtolower($value[strlen($value) - 1]);
        $bytes = (int) $value;

        return match ($unit) {
            'g' => $bytes * 1024 * 1024 * 1024,
            'm' => $bytes * 1024 * 1024,
            'k' => $bytes * 1024,
            default => $bytes,
        };
    }
}
