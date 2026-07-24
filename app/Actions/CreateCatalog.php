<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Enums\VisibilityEnum;
use App\Helpers\TextSanitizer;
use App\Jobs\LogUserAction;
use App\Models\Account;
use App\Models\Catalog;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

/**
 * Create a collection within an account. Only owners and editors may do so.
 */
class CreateCatalog
{
    private Catalog $catalog;

    /**
     * @param  array<string, mixed>|null  $settings
     * @param  array<int, int>  $catalogTypeIds
     */
    public function __construct(
        private readonly User $user,
        private readonly Account $account,
        private string $name,
        private ?string $description = null,
        private ?string $emoji = null,
        private string $visibility = VisibilityEnum::Private->value,
        private ?string $currency = null,
        private ?array $settings = null,
        private array $catalogTypeIds = [],
    ) {}

    public function execute(): Catalog
    {
        $this->validate();
        $this->sanitize();
        $this->create();
        $this->syncCatalogTypes();
        $this->stampAuthor();
        $this->log();

        return $this->catalog;
    }

    private function validate(): void
    {
        if (! $this->userCanManageCatalogs()) {
            throw new ModelNotFoundException('Account not found');
        }

        if (VisibilityEnum::tryFrom($this->visibility) === null) {
            throw ValidationException::withMessages(['visibility' => 'Invalid visibility']);
        }
    }

    private function userCanManageCatalogs(): bool
    {
        return in_array(
            $this->account->roleFor($this->user),
            [PermissionEnum::Owner->value, PermissionEnum::Editor->value],
            true,
        );
    }

    private function sanitize(): void
    {
        $this->name = TextSanitizer::plainText($this->name);
        $this->description = TextSanitizer::nullablePlainText($this->description);
        $this->emoji = TextSanitizer::nullablePlainText($this->emoji);
    }

    private function create(): void
    {
        $this->catalog = Catalog::query()->create([
            'account_id' => $this->account->id,
            'name' => $this->name,
            'description' => $this->description,
            'emoji' => $this->emoji,
            'visibility' => $this->visibility,
            'currency' => $this->currency,
            'settings' => $this->settings,
        ]);
    }

    private function syncCatalogTypes(): void
    {
        $ids = $this->account->catalogTypes()
            ->whereIn('id', $this->catalogTypeIds)
            ->pluck('id')
            ->all();

        $this->catalog->catalogTypes()->sync($ids);
    }

    private function stampAuthor(): void
    {
        $this->catalog->created_by_id = $this->user->id;
        $this->catalog->created_by_name = $this->user->getFullName();
        $this->catalog->updated_by_id = $this->user->id;
        $this->catalog->updated_by_name = $this->user->getFullName();
        $this->catalog->save();
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            action: UserActionEnum::CatalogCreation,
            parameters: ['name' => $this->catalog->name],
        )->onQueue('low');
    }
}
