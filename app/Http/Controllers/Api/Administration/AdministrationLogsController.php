<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Administration;

use App\Http\Controllers\Controller;
use App\Http\Resources\LogResource;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AdministrationLogsController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $logs = Log::query()
            ->where('user_id', $request->user()->id)
            ->with(['user', 'vault'])
            ->latest()
            ->paginate(10);

        return LogResource::collection($logs);
    }

    public function show(Request $request): LogResource
    {
        $id = $request->route()->parameter('log');

        $logEntry = Log::query()
            ->where('user_id', $request->user()->id)
            ->with(['user', 'vault'])
            ->findOrFail($id);

        return new LogResource($logEntry);
    }
}
