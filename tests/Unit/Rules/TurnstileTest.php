<?php

declare(strict_types=1);

use App\Rules\Turnstile;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

it('passes a token cloudflare accepts', function () {
    config()->set('turnstile.secret_key', 'secret-of-the-one-with-the-widget');
    Http::fake(['challenges.cloudflare.com/*' => Http::response(['success' => true])]);

    $validator = Validator::make(
        ['cf-turnstile-response' => 'token-from-the-widget'],
        ['cf-turnstile-response' => [new Turnstile]],
    );

    expect($validator->passes())->toBeTrue();

    Http::assertSent(function ($request): bool {
        return $request->url() === 'https://challenges.cloudflare.com/turnstile/v0/siteverify'
            && $request->method() === 'POST'
            && $request->data() === [
                'secret' => 'secret-of-the-one-with-the-widget',
                'response' => 'token-from-the-widget',
            ];
    });
});

it('fails a token cloudflare rejects', function () {
    config()->set('turnstile.secret_key', 'secret-of-the-one-with-the-widget');
    Http::fake(['challenges.cloudflare.com/*' => Http::response(['success' => false, 'error-codes' => ['invalid-input-response']])]);

    $validator = Validator::make(
        ['cf-turnstile-response' => 'token-that-was-already-used'],
        ['cf-turnstile-response' => [new Turnstile]],
    );

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->first('cf-turnstile-response'))
        ->toBe('The anti-spam verification failed. Please try again.');
});

it('fails closed when cloudflare cannot be reached', function () {
    config()->set('turnstile.secret_key', 'secret-of-the-one-with-the-widget');
    Http::fake(fn () => throw new ConnectionException('Could not resolve host.'));

    $validator = Validator::make(
        ['cf-turnstile-response' => 'token-from-the-widget'],
        ['cf-turnstile-response' => [new Turnstile]],
    );

    expect($validator->fails())->toBeTrue();
});
