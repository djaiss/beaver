<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Account;

use App\Actions\InviteToAccount;
use App\Actions\RemoveAccountMember;
use App\Actions\UpdateMemberRole;
use App\Enums\PermissionEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\InvitationResource;
use App\Http\Resources\MemberResource;
use App\Traits\EnsuresAccountOwner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Validation\Rules\Enum;

class MemberController extends Controller
{
    use EnsuresAccountOwner;

    public function index(Request $request): AnonymousResourceCollection
    {
        $this->ensureOwner($request);

        $perPage = max(1, min((int) $request->query('per_page', 10), config('app.maximum_items_per_page')));

        $members = $request->user()->account->users()
            ->orderBy('id')
            ->paginate($perPage);

        return MemberResource::collection($members);
    }

    public function show(Request $request): JsonResponse
    {
        $this->ensureOwner($request);

        $memberId = $request->route()->parameter('member');

        $member = $request->user()->account->users()->findOrFail($memberId);

        return new MemberResource($member)
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Inviting someone does not create a member: it creates an invitation and
     * emails it. The member appears once the invitation is claimed on the web.
     */
    public function create(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255'],
            'role' => ['required', new Enum(PermissionEnum::class)],
        ]);

        $invitation = new InviteToAccount(
            user: $request->user(),
            account: $request->user()->account,
            email: $validated['email'],
            role: $validated['role'],
        )->execute();

        return new InvitationResource($invitation)
            ->response()
            ->setStatusCode(201);
    }

    public function update(Request $request): JsonResponse
    {
        $memberId = $request->route()->parameter('member');

        $validated = $request->validate([
            'role' => ['required', new Enum(PermissionEnum::class)],
        ]);

        $account = $request->user()->account;
        $member = $account->users()->findOrFail($memberId);

        $member = new UpdateMemberRole(
            user: $request->user(),
            account: $account,
            member: $member,
            role: $validated['role'],
        )->execute();

        return new MemberResource($member)
            ->response()
            ->setStatusCode(200);
    }

    public function destroy(Request $request): Response
    {
        $memberId = $request->route()->parameter('member');

        $account = $request->user()->account;
        $member = $account->users()->findOrFail($memberId);

        new RemoveAccountMember(
            user: $request->user(),
            account: $account,
            member: $member,
        )->execute();

        return response()->noContent(204);
    }
}
