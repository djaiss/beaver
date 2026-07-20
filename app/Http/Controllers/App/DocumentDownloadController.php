<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentDownloadController extends Controller
{
    /**
     * Stream a stored document file. Documents live on the private disk, so they
     * are served through here rather than a public URL, and only to members of
     * the account the document belongs to. An external document has no file to
     * stream, so it is not reachable here.
     */
    public function show(Request $request, Document $document): StreamedResponse
    {
        $account = $request->user()->account;

        if ($document->account_id !== $account->id) {
            abort(404);
        }

        if ($document->path === null) {
            abort(404);
        }

        $disk = Storage::disk((string) config('filesystems.default'));

        if (! $disk->exists($document->path)) {
            abort(404);
        }

        return $disk->response($document->path, name: $document->name, headers: [
            'Content-Type' => (string) $document->mime_type,
            'Cache-Control' => 'private, max-age=3600',
        ]);
    }
}
