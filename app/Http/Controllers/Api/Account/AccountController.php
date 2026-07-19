<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Account;

use App\Actions\DestroyAccount;
use App\Actions\UpdateAccount;
use App\Http\Controllers\Controller;
use App\Http\Resources\AccountResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class AccountController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        return new AccountResource($request->user()->account)
            ->response()
            ->setStatusCode(200);
    }

    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'currency_code' => ['required', 'string', Rule::in(array_keys(config('currencies')))],
        ]);

        $account = new UpdateAccount(
            user: $request->user(),
            account: $request->user()->account,
            name: $validated['name'],
            currencyCode: $validated['currency_code'],
        )->execute();

        return new AccountResource($account)
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Delete the account and everything in it, including every other member.
     * There is no undo, and the token used to make the call stops working.
     */
    public function destroy(Request $request): Response
    {
        new DestroyAccount(
            user: $request->user(),
            account: $request->user()->account,
        )->execute();

        return response()->noContent(204);
    }
}
