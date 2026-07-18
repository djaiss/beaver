<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\TrashableEnum;
use App\Models\Account;
use App\Models\Category;
use App\Models\Collection as CollectionModel;
use App\Models\Copy;
use App\Models\Item;
use App\Models\Set;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Gathers everything an account has soft deleted, across the five tables that
 * support it, into one list the trash screen can render.
 *
 * Deleting a parent does not stamp its children, so this only ever surfaces the
 * rows a user deleted on purpose.
 *
 * @phpstan-type TrashedModel Category|CollectionModel|Copy|Item|Set
 */
class Trash
{
    public function __construct(
        private readonly Account $account,
    ) {}

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function entries(): Collection
    {
        $entries = new Collection;

        foreach (TrashableEnum::cases() as $type) {
            $entries = $entries->concat(
                $this->query($type)->get()->map(fn (Model $model): array => $this->entry($type, $model)),
            );
        }

        // Whatever expires first is the most urgent, so it goes to the top.
        return $entries->sortBy('days_left')->values();
    }

    /**
     * The trashed records of one type belonging to the account.
     *
     * @return Builder<TrashedModel>
     */
    public function query(TrashableEnum $type): Builder
    {
        $modelClass = $type->modelClass();

        /** @var Builder<TrashedModel> $query */
        $query = $modelClass::onlyTrashed();

        return match ($type) {
            TrashableEnum::Collection, TrashableEnum::Set => $query->where('account_id', $this->account->id),

            TrashableEnum::Item, TrashableEnum::Category => $query->whereIn(
                'collection_id',
                CollectionModel::withTrashed()->where('account_id', $this->account->id)->select('id'),
            ),

            TrashableEnum::Copy => $query->whereIn(
                'item_id',
                Item::withTrashed()->whereIn(
                    'collection_id',
                    CollectionModel::withTrashed()->where('account_id', $this->account->id)->select('id'),
                )->select('id'),
            ),
        };
    }

    /**
     * @param  TrashedModel  $model
     * @return array<string, mixed>
     */
    private function entry(TrashableEnum $type, Model $model): array
    {
        return [
            'type' => $type,
            'id' => $model->id,
            'name' => $this->name($model),
            'subtitle' => $this->subtitle($model),
            'deleted_at' => $model->deleted_at,
            'deleted_by_name' => $model->deleted_by_name,
            'days_left' => $this->daysLeft($model),
        ];
    }

    /**
     * @param  TrashedModel  $model
     */
    private function daysLeft(Model $model): int
    {
        $expiresAt = $model->deleted_at->copy()->addDays(config('trash.retention_days'));

        return max(0, (int) ceil(now()->diffInDays($expiresAt, absolute: false)));
    }

    /**
     * A copy has no name of its own, so it borrows its row number.
     *
     * @param  TrashedModel  $model
     */
    private function name(Model $model): string
    {
        if ($model instanceof Copy) {
            return __('Copy #:id', ['id' => $model->id]);
        }

        return $model->name;
    }

    /**
     * @param  TrashedModel  $model
     */
    private function subtitle(Model $model): string
    {
        return match (true) {
            $model instanceof Item => $this->parentCollectionName($model),
            $model instanceof Copy => $this->copyItemName($model),
            default => $this->itemCount($model),
        };
    }

    private function itemCount(Category|CollectionModel|Set $model): string
    {
        $count = $model->items()->count();

        return trans_choice(':count item|:count items', $count, ['count' => $count]);
    }

    private function parentCollectionName(Item $item): string
    {
        $collection = CollectionModel::withTrashed()->find($item->collection_id);

        return $collection === null ? '' : __('in :name', ['name' => $collection->name]);
    }

    private function copyItemName(Copy $copy): string
    {
        $item = Item::withTrashed()->find($copy->item_id);

        return $item === null ? '' : $item->name;
    }
}
