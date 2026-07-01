<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Helpers\TextSanitizer;
use App\Jobs\LogUserAction;
use App\Models\Person;
use App\Models\SpecialDate;
use App\Models\User;
use App\Models\Vault;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CreateSpecialDate
{
    private SpecialDate $specialDate;

    public function __construct(
        private readonly User $user,
        private readonly Vault $vault,
        private readonly Person $person,
        private string $name,
        private readonly bool $shouldBeReminded = false,
        private readonly ?int $year = null,
        private readonly ?int $month = null,
        private readonly ?int $day = null,
    ) {}

    public function execute(): SpecialDate
    {
        $this->sanitize();
        $this->validate();
        $this->create();
        $this->log();

        return $this->specialDate;
    }

    private function sanitize(): void
    {
        $this->name = TextSanitizer::plainText($this->name);
    }

    private function validate(): void
    {
        if ($this->user->isPartOfVault($this->vault) === false) {
            throw new ModelNotFoundException('Vault not found');
        }

        if ($this->user->memberOf($this->vault)->role === PermissionEnum::Viewer->value) {
            throw new ModelNotFoundException('Permission denied');
        }

        if ($this->person->vault_id !== $this->vault->id) {
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

    private function create(): void
    {
        $this->specialDate = SpecialDate::query()->create([
            'vault_id' => $this->vault->id,
            'person_id' => $this->person->id,
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
            vault: $this->vault,
            user: $this->user,
            action: UserActionEnum::SpecialDateCreation,
            parameters: ['name' => $this->name],
        )->onQueue('low');
    }
}
