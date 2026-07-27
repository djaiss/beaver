<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Instance;

use App\Http\Controllers\Controller;
use App\Models\UserDeletionReason;
use Illuminate\View\View;

/**
 * What people wrote when they deleted their user. The rows are kept on purpose
 * after the person is gone, and are not linked to anyone, so this page only
 * lists them. Like the rest of the instance panel it is English only, so its
 * copy is written as plain strings rather than through __().
 */
class DeletionReasonController extends Controller
{
    /**
     * Every reason, newest first. The reason itself is encrypted at rest, so it
     * cannot be searched or sorted on in SQL, which is why the page is a plain
     * list.
     */
    public function index(): View
    {
        return view('app.instance.deletionReasons.index', [
            'reasons' => UserDeletionReason::query()
                ->latest('id')
                ->paginate(25),
            'totalCount' => UserDeletionReason::query()->count(),
        ]);
    }
}
