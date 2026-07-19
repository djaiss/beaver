<?php

declare(strict_types=1);

namespace App\Http\Controllers\Concerns;

use App\Enums\PermissionEnum;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

/**
 * Shared by the API endpoints that read what only an owner may see, such as the
 * roster of the account. The write endpoints do not need this: their action
 * checks the role itself.
 *
 * Failing looks like a missing record rather than a refusal, which is how the
 * rest of the API answers a request the user is not allowed to make.
 */
trait EnsuresAccountOwner
{
    private function ensureOwner(Request $request): void
    {
        $account = $request->user()->account;

        if ($account->roleFor($request->user()) !== PermissionEnum::Owner->value) {
            throw new ModelNotFoundException('Account not found');
        }
    }
}
