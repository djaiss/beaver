<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Adminland;

use App\Actions\JoinVault;
use App\Http\Controllers\Controller;
use App\Http\Resources\MemberResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MemberController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $vault = $request->attributes->get('vault');

        $perPage = max(1, min((int) $request->query('per_page', 10), config('app.maximum_items_per_page')));

        $members = $vault->members()
            ->with('user')
            ->orderByDesc('joined_at')
            ->paginate($perPage);

        return MemberResource::collection($members);
    }

    public function show(Request $request): JsonResponse
    {
        $vault = $request->attributes->get('vault');
        $memberId = $request->route()->parameter('memberId');

        $member = $vault->members()->with('user')->findOrFail($memberId);

        return new MemberResource($member)
            ->response()
            ->setStatusCode(200);
    }

    public function create(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'invitation_code' => [
                'required',
                'string',
                'max:64',
            ],
        ]);

        $vault = new JoinVault(
            user: $request->user(),
            invitationCode: $validated['invitation_code'],
        )->execute();

        $member = $vault->members()
            ->with('user')
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        return new MemberResource($member)
            ->response()
            ->setStatusCode(201);
    }
}
