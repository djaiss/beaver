<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Helpers\TextSanitizer;
use App\Jobs\LogUserAction;
use App\Models\SpecialDate;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UpdateSpecialDate
{
    public function __construct(
        private readonly User $user,
        private readonly SpecialDate $specialDate,
        private string $name,
        private readonly bool $shouldBeReminded,
        private readonly ?int $year = null,
        private readonly ?int $month = null,
        private readonly ?int $day = null,
    ) {}

    public function execute(): SpecialDate
    {
        $this->sanitize();
        $this->validate();
        $this->update();
        $this->log();

        return $this->specialDate;
    }

    private function sanitize(): void
    {
        $this->name = TextSanitizer::plainText($this->name);
    }

    private function validate(): void
    {
        if ($this->user->isPartOfVault($this->specialDate->vault) === false) {
            throw new ModelNotFoundException('Special date not found');
        }

        if ($this->user->memberOf($this->specialDate->vault)->role === PermissionEnum::Viewer->value) {
            throw new ModelNotFoundException('Permission denied');
        }

        if ($this->specialDate->person->vault_id !== $this->specialDate->vault_id) {
            throw new ModelNotFoundException('Person not found');
        }

        $this->validateDate();
    }

    private function validateDate(): void
    {
        if ($this->year !== null && ($this->year < 1 || $this->year > 9999)) {
            throw new ModelNotFoundException('Invalid year');
        }

        if ($this->month !== null && ($this->month < 1 || $this->month > 12)) {
            throw new ModelNotFoundException('Invalid month');
        }

        if ($this->day !== null && ($this->month === null || checkdate($this->month, $this->day, $this->year ?? 2000) === false)) {
            throw new ModelNotFoundException('Invalid day');
        }
    }

    private function update(): void
    {
        $this->specialDate->update([
            'name' => $this->name,
            'should_be_reminded' => $this->shouldBeReminded,
            'year' => $this->year,
            'month' => $this->month,
            'day' => $this->day,
        ]);
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            vault: $this->specialDate->vault,
            user: $this->user,
            action: UserActionEnum::SpecialDateUpdate,
            parameters: ['name' => $this->name],
        )->onQueue('low');
    }
}
