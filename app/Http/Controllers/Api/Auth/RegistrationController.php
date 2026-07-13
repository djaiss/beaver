<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Auth;

use App\Actions\CreateAccount;
use App\Helpers\TextSanitizer;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponses;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class RegistrationController extends Controller
{
    use ApiResponses;

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                'unique:'.User::class,
                'disposable_email',
            ],
            'password' => [
                'required',
                'string',
                'max:255',
                'confirmed',
                Password::min(8)->uncompromised(),
            ],
            'device_name' => ['nullable', 'string', 'max:255'],
        ]);

        $user = new CreateAccount(
            email: mb_strtolower((string) $validated['email']),
            password: $validated['password'],
            firstName: $validated['first_name'],
            lastName: $validated['last_name'],
        )->execute();

        event(new Registered($user));

        $token = $user->createToken($this->tokenName($validated['device_name'] ?? null))->plainTextToken;

        return $this->success('Account created', 201, [
            'token' => $token,
        ]);
    }

    /**
     * Build a human-readable name for the issued token. Naming it after the
     * device the user registered from means each token is clearly
     * identifiable in the list of personal access tokens.
     */
    private function tokenName(?string $deviceName): string
    {
        $deviceName = TextSanitizer::plainText((string) $deviceName);

        if ($deviceName === '') {
            return 'Login from an unknown device';
        }

        return 'Login from '.$deviceName;
    }
}
