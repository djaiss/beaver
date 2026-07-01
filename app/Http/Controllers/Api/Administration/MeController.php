<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Administration;

use App\Actions\UpdateUserInformation;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MeController extends Controller
{
    /**
     * Get the information about the logged user.
     */
    public function show(Request $request): UserResource
    {
        return new UserResource($request->user());
    }

    /**
     * Update your profile.
     */
    public function update(Request $request): UserResource
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'nickname' => ['nullable', 'string', 'max:100'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($request->user()->id),
                'disposable_email',
            ],
            'locale' => ['required', 'string', 'max:5', Rule::in(config('app.supported_locales'))],
            'time_format_24h' => ['required', Rule::in(['true', 'false'])],
        ]);

        new UpdateUserInformation(
            user: $request->user(),
            email: mb_strtolower((string) $validated['email']),
            firstName: $validated['first_name'],
            lastName: $validated['last_name'],
            nickname: $validated['nickname'] ?? null,
            locale: $validated['locale'],
            timeFormat24h: $validated['time_format_24h'] === 'true',
        )->execute();

        return new UserResource($request->user()->refresh());
    }
}
