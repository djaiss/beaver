<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\TrashableEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\Account;
use App\Models\User;
use App\Services\Trash;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Permanently delete everything an account has in its trash, without waiting for
 * the retention timer to run out. Only owners and editors may do so.
 */
class EmptyTrash
{
    /**
     * Children are deleted before their parents, because the trash queries reach
     * items and copies through their collection.
     *
     * @var list<TrashableEnum>
     */
    private const DELETION_ORDER = [
        TrashableEnum::Copy,
        TrashableEnum::Item,
        TrashableEnum::Category,
        TrashableEnum::Collection,
        TrashableEnum::Set,
    ];

    public function __construct(
        private readonly User $user,
        private readonly Account $account,
    ) {}

    /**
     * The number of objects that were permanently deleted.
     */
    public function execute(): int
    {
        $this->validate();

        $deleted = $this->purge();

        $this->log($deleted);

        return $deleted;
    }

    private function validate(): void
    {
        if (! $this->account->allowsManagementBy($this->user)) {
            throw new ModelNotFoundException('Account not found');
        }
    }

    private function purge(): int
    {
        $trash = new Trash(account: $this->account);
        $deleted = 0;

        foreach (self::DELETION_ORDER as $type) {
            $models = $trash->query($type)->get();

            $models->each(function (Model $model) use (&$deleted): void {
                $model->forceDelete();
                $deleted++;
            });
        }

        return $deleted;
    }

    private function log(int $deleted): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            action: UserActionEnum::TrashEmptied,
            parameters: [
                'count' => $deleted,
            ],
        )->onQueue('low');
    }
}
