<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Account;
use Closure;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class CheckAccount
{
    /**
     * Check if the user is a member of the account.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $id = (int) $request->route()->parameter('accountId');

        try {
            $account = Account::query()->findOrFail($id);

            $member = $request->user()->memberFor($account);
            abort_unless($member !== null, 403);

            $request->attributes->add(['account' => $account]);
            $request->attributes->add(['member' => $member]);

            View::share('account', $account);
            View::share('member', $member);

            return $next($request);
        } catch (ModelNotFoundException) {
            abort(404);
        }
    }
}
