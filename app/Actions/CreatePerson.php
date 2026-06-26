<?php

declare(strict_types=1);

namespace App\Actions;

use App\Cache\PersonsListCache;
use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Helpers\TextSanitizer;
use App\Jobs\LogUserAction;
use App\Models\Gender;
use App\Models\Person;
use App\Models\User;
use App\Models\Vault;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;

class CreatePerson
{
    private Person $person;

    public function __construct(
        private readonly User $user,
        private readonly Vault $vault,
        private readonly ?Gender $gender = null,
        private ?string $firstName = null,
        private ?string $middleName = null,
        private ?string $lastName = null,
        private ?string $nickname = null,
        private ?string $maidenName = null,
        private ?string $suffix = null,
        private ?string $prefix = null,
        private ?string $kidsStatus = null,
        private readonly bool $canBeDeleted = true,
        private readonly bool $isListed = true,
    ) {}

    public function execute(): Person
    {
        $this->sanitize();
        $this->validate();
        $this->create();
        $this->generateSlug();
        $this->log();
        $this->refreshCache();

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
        if ($this->user->isPartOfVault($this->vault) === false) {
            throw new ModelNotFoundException('Vault not found');
        }

        $member = $this->user->memberOf($this->vault);

        if ($member->role === PermissionEnum::Viewer->value) {
            throw new ModelNotFoundException('Permission denied');
        }

        if ($this->gender instanceof Gender && $this->gender->vault_id !== $this->vault->id) {
            throw new ModelNotFoundException('Gender not found');
        }
    }

    private function create(): void
    {
        $this->person = Person::query()->create([
            'vault_id' => $this->vault->id,
            'gender_id' => $this->gender?->id,
            'kids_status' => $this->kidsStatus,
            'first_name' => $this->firstName,
            'middle_name' => $this->middleName,
            'last_name' => $this->lastName,
            'nickname' => $this->nickname,
            'maiden_name' => $this->maidenName,
            'suffix' => $this->suffix,
            'prefix' => $this->prefix,
            'can_be_deleted' => $this->canBeDeleted,
            'is_listed' => $this->isListed,
        ]);
    }

    private function generateSlug(): void
    {
        $name = "{$this->person->first_name} {$this->person->last_name}";
        $sluggedName = Str::of($name)->slug('-');
        $slug = "{$this->person->id}-{$sluggedName}";

        $this->person->slug = $slug;
        $this->person->save();
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            vault: $this->vault,
            user: $this->user,
            action: UserActionEnum::PersonCreation,
            parameters: ['name' => $this->person->first_name],
        )->onQueue('low');
    }

    private function refreshCache(): void
    {
        PersonsListCache::make(
            vaultId: $this->vault->id,
        )->refresh();
    }
}
