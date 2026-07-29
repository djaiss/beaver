<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

/**
 * Checks the token a visitor's Turnstile widget produced against Cloudflare.
 *
 * The rule fails closed: a token Cloudflare rejects, an answer we cannot make
 * sense of and an endpoint we cannot reach all fail the field. That means an
 * instance with the widget turned on cannot sign anybody in while Cloudflare is
 * unreachable, which is the point of a verification.
 */
class Turnstile implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try {
            $response = Http::timeout(10)
                ->asForm()
                ->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                    'secret' => config('turnstile.secret_key'),
                    'response' => $value,
                ]);
        } catch (ConnectionException) {
            $fail(__('The anti-spam verification failed. Please try again.'));

            return;
        }

        if ($response->json('success') === true) {
            return;
        }

        $fail(__('The anti-spam verification failed. Please try again.'));
    }
}
