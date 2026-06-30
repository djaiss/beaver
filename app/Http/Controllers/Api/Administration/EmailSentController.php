<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Administration;

use App\Http\Controllers\Controller;
use App\Http\Resources\EmailSentResource;
use App\Models\EmailSent;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class EmailSentController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = max(1, min((int) $request->query('per_page', 10), config('app.maximum_items_per_page')));

        $emails = EmailSent::query()
            ->where('user_id', $request->user()->id)
            ->latest('sent_at')
            ->paginate($perPage);

        return EmailSentResource::collection($emails);
    }

    public function show(Request $request): EmailSentResource
    {
        $id = $request->route()->parameter('email');

        $email = EmailSent::query()
            ->where('user_id', $request->user()->id)
            ->findOrFail($id);

        return new EmailSentResource($email);
    }
}
