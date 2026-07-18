<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\TrashableEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\Account;
use App\Models\Category;
use App\Models\Collection;
use App\Models\Copy;
use App\Models\Item;
use App\Models\Set;
use App\Models\User;
use App\Services\Trash;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Restore a soft deleted object from the trash. Only owners and editors of the
 * account may do so, and only for objects belonging to that account.
 */
class RestoreFromTrash
{
    public function __construct(
        private readonly User $user,
        private readonly Account $account,
        private readonly TrashableEnum $type,
        private readonly int $objectId,
    ) {}

    public function execute(): Category|Collection|Copy|Item|Set
    {
        $this->validate();

        $model = $this->find();

        $model->restore();

        $this->log($model);

        return $model;
    }

    private function validate(): void
    {
        if (! $this->account->allowsManagementBy($this->user)) {
            throw new ModelNotFoundException('Account not found');
        }
    }

    private function find(): Category|Collection|Copy|Item|Set
    {
        return new Trash(account: $this->account)
            ->query($this->type)
            ->findOrFail($this->objectId);
    }

    private function log(Category|Collection|Copy|Item|Set $model): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            action: UserActionEnum::TrashRestoration,
            parameters: [
                'type' => $this->type->label(),
                'name' => $model instanceof Copy ? __('Copy #:id', ['id' => $model->id]) : $model->name,
            ],
        )->onQueue('low');
    }
}
