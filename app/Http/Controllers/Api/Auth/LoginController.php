<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Auth;

use App\Actions\VerifyTwoFactorCode;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use ApiResponses;

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'max:255'],
            'code' => ['nullable', 'string', 'max:255'],
        ]);

        if (! Auth::attempt([
            'email' => $validated['email'],
            'password' => $validated['password'],
        ])) {
            return $this->error('Invalid credentials', 401);
        }

        $user = User::query()->where('email', $validated['email'])->first();

        // If the user has enabled two-factor authentication, a valid TOTP or
        // recovery code must be provided before an API token is issued.
        // Otherwise the API would be a complete bypass of the user's 2FA.
        if (! is_null($user->two_factor_confirmed_at)) {
            if (empty($validated['code'])) {
                return $this->error('Two-factor authentication code required', 401);
            }

            if (! new VerifyTwoFactorCode(
                user: $user,
                code: (string) $validated['code'],
            )->execute()) {
                return $this->error('Invalid two-factor authentication code', 401);
            }
        }

        $tokenName = 'API token for '.$user->email;

        $token = $user->createToken($tokenName)->plainTextToken;

        return $this->success('Authenticated', 200, [
            'token' => $token,
        ]);
    }

    public function destroy(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->success('Logged out successfully', 200);
    }
}
