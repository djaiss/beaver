<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\ItemPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ItemPhotoController extends Controller
{
    /**
     * Stream a photo file. Photos live on the private disk, so they are served
     * through here rather than a public URL, and only to members of the account
     * the photo belongs to.
     */
    public function show(Request $request, ItemPhoto $itemPhoto): StreamedResponse
    {
        $account = $request->user()->account;

        if ($itemPhoto->item->collection->account_id !== $account->id) {
            abort(404);
        }

        $disk = Storage::disk((string) config('filesystems.default'));

        if (! $disk->exists($itemPhoto->path)) {
            abort(404);
        }

        return $disk->response($itemPhoto->path, headers: [
            'Content-Type' => $itemPhoto->mime_type,
            'Cache-Control' => 'private, max-age=3600',
        ]);
    }
}
