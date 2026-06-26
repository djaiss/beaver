<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Auth;

use App\Actions\CreateAccount;
use App\Http\Controllers\Controller;
use App\Http\Requests\App\Auth\StoreAccountRequest;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RegistrationController extends Controller
{
    public function create(): View
    {
        $quotes = config('quotes');
        $randomQuote = $quotes[array_rand($quotes)];

        return view('app.auth.register', [
            'quote' => $randomQuote,
        ]);
    }

    public function store(StoreAccountRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $user = new CreateAccount(
            email: mb_strtolower((string) $validated['email']),
            password: $validated['password'],
            firstName: $validated['first_name'],
            lastName: $validated['last_name'],
        )->execute();

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('vault.index', absolute: false));
    }
}
