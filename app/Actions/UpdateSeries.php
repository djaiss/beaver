<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\UserActionEnum;
use App\Helpers\TextSanitizer;
use App\Jobs\LogUserAction;
use App\Models\Series;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Update a series' name and description. Only owners and editors of its
 * account may do so.
 */
class UpdateSeries
{
    public function __construct(
        private readonly User $user,
        private readonly Series $series,
        private string $name,
        private ?string $description = null,
    ) {}

    public function execute(): Series
    {
        $this->validate();
        $this->sanitize();
        $this->update();
        $this->log();

        return $this->series;
    }

    private function validate(): void
    {
        if (! $this->series->account->allowsManagementBy($this->user)) {
            throw new ModelNotFoundException('Account not found');
        }
    }

    private function sanitize(): void
    {
        $this->name = TextSanitizer::plainText($this->name);
        $this->description = TextSanitizer::nullablePlainText($this->description);
    }

    private function update(): void
    {
        $this->series->name = $this->name;
        $this->series->description = $this->description;
        $this->series->updated_by_id = $this->user->id;
        $this->series->updated_by_name = $this->user->getFullName();
        $this->series->save();
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            action: UserActionEnum::SeriesUpdate,
            parameters: ['name' => $this->series->name],
        )->onQueue('low');
    }
}
