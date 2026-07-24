<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Account;

use App\Http\Controllers\Controller;
use App\Http\Resources\InvitationResource;
use App\Traits\EnsuresAccountOwner;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class InvitationController extends Controller
{
    use EnsuresAccountOwner;

    /**
     * The invitations still waiting to be claimed. Expired ones are left out,
     * matching what the members screen shows. Owner only, like that screen.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->ensureOwner($request);

        $perPage = max(1, min((int) $request->query('per_page', 10), config('app.maximum_items_per_page')));

        $invitations = $request->user()->account->invitations()
            ->whereNull('accepted_at')
            ->where('expires_at', '>', now())
            ->orderBy('id')
            ->paginate($perPage);

        return InvitationResource::collection($invitations);
    }
}
