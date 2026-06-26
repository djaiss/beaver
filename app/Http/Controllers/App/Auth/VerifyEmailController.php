<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    public function index(EmailVerificationRequest $request): RedirectResponse
    {
        $vaultIndexRoute = route('vault.index', absolute: false);
        $verifiedRedirect = "{$vaultIndexRoute}?verified=1";

        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended($verifiedRedirect);
        }

        if ($request->user()->markEmailAsVerified()) {
            /** @var MustVerifyEmail $user */
            $user = $request->user();

            event(new Verified($user));
        }

        return redirect()->intended($verifiedRedirect);
    }
}
