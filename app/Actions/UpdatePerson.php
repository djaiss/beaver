<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Helpers\TextSanitizer;
use App\Jobs\LogUserAction;
use App\Models\Gender;
use App\Models\Person;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;

class UpdatePerson
{
    public function __construct(
        private readonly User $user,
        private readonly Person $person,
        private readonly ?Gender $gender = null,
        private ?string $firstName = null,
        private ?string $middleName = null,
        private ?string $lastName = null,
        private ?string $nickname = null,
        private ?string $maidenName = null,
        private ?string $suffix = null,
        private ?string $prefix = null,
        private ?string $kidsStatus = null,
        private readonly ?bool $canBeDeleted = null,
        private readonly ?bool $isListed = null,
    ) {}

    public function execute(): Person
    {
        $this->sanitize();
        $this->validate();
        $this->update();
        $this->generateSlug();
        $this->log();

        return $this->person;
    }

    private function sanitize(): void
    {
        $this->firstName = TextSanitizer::nullablePlainText($this->firstName);
        $this->middleName = TextSanitizer::nullablePlainText($this->middleName);
        $this->lastName = TextSanitizer::nullablePlainText($this->lastName);
        $this->nickname = TextSanitizer::nullablePlainText($this->nickname);
        $this->maidenName = TextSanitizer::nullablePlainText($this->maidenName);
        $this->suffix = TextSanitizer::nullablePlainText($this->suffix);
        $this->prefix = TextSanitizer::nullablePlainText($this->prefix);
        $this->kidsStatus = TextSanitizer::nullablePlainText($this->kidsStatus);
    }

    private function validate(): void
    {
        if ($this->user->isPartOfVault($this->person->vault) === false) {
            throw new ModelNotFoundException('Person not found');
        }

        $member = $this->user->memberOf($this->person->vault);

        if ($member->role === PermissionEnum::Viewer->value) {
            throw new ModelNotFoundException('Permission denied');
        }

        if ($this->gender instanceof Gender && $this->gender->vault_id !== $this->person->vault_id) {
            throw new ModelNotFoundException('Gender not found');
        }

    }

    private function update(): void
    {
        $data = [
            'gender_id' => $this->gender?->id,
            'kids_status' => $this->kidsStatus,
            'first_name' => $this->firstName,
            'middle_name' => $this->middleName,
            'last_name' => $this->lastName,
            'nickname' => $this->nickname,
            'maiden_name' => $this->maidenName,
            'suffix' => $this->suffix,
            'prefix' => $this->prefix,
        ];

        if ($this->canBeDeleted !== null) {
            $data['can_be_deleted'] = $this->canBeDeleted;
        }

        if ($this->isListed !== null) {
            $data['is_listed'] = $this->isListed;
        }

        $this->person->update($data);
    }

    private function generateSlug(): void
    {
        $name = $this->person->first_name.' '.$this->person->last_name;
        $slug = $this->person->id.'-'.Str::of($name)->slug('-');

        $this->person->slug = $slug;
        $this->person->save();
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            vault: $this->person->vault,
            user: $this->user,
            action: UserActionEnum::PersonUpdate,
            parameters: ['name' => $this->person->first_name],
        )->onQueue('low');
    }
}
