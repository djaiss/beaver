<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Adminland;

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

        $members = $vault->members()
            ->with('user')
            ->orderByDesc('joined_at')
            ->get();

        return MemberResource::collection($members);
    }

    public function show(Request $request, int $id, int $memberId): JsonResponse
    {
        $vault = $request->attributes->get('vault');

        $member = $vault->members()->with('user')->findOrFail($memberId);

        return new MemberResource($member)
            ->response()
            ->setStatusCode(200);
    }
}
