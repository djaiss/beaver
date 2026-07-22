<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\UserActionEnum;
use App\Helpers\TextSanitizer;
use App\Jobs\LogUserAction;
use App\Models\Account;
use App\Models\Series;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Create a series in an account. Only owners and editors may do so.
 */
class CreateSeries
{
    private Series $series;

    public function __construct(
        private readonly User $user,
        private readonly Account $account,
        private string $name,
        private ?string $description = null,
    ) {}

    public function execute(): Series
    {
        $this->validate();
        $this->sanitize();
        $this->create();
        $this->stampAuthor();
        $this->log();

        return $this->series;
    }

    private function validate(): void
    {
        if (! $this->account->allowsManagementBy($this->user)) {
            throw new ModelNotFoundException('Account not found');
        }
    }

    private function sanitize(): void
    {
        $this->name = TextSanitizer::plainText($this->name);
        $this->description = TextSanitizer::nullablePlainText($this->description);
    }

    private function create(): void
    {
        $this->series = Series::query()->create([
            'account_id' => $this->account->id,
            'name' => $this->name,
            'description' => $this->description,
        ]);
    }

    private function stampAuthor(): void
    {
        $this->series->created_by_id = $this->user->id;
        $this->series->created_by_name = $this->user->getFullName();
        $this->series->updated_by_id = $this->user->id;
        $this->series->updated_by_name = $this->user->getFullName();
        $this->series->save();
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            action: UserActionEnum::SeriesCreation,
            parameters: ['name' => $this->series->name],
        )->onQueue('low');
    }
}
