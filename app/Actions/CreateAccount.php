<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Helpers\TextSanitizer;
use App\Jobs\LogUserAction;
use App\Models\Account;
use App\Models\User;

/**
 * Create an account and its first user — the person who signed up, who becomes
 * the owner of the account. Returns that first user.
 */
class CreateAccount
{
    private Account $account;

    private User $user;

    public function __construct(
        private string $email,
        private readonly string $password,
        private string $firstName,
        private string $lastName,
    ) {}

    public function execute(): User
    {
        $this->sanitize();
        $this->createAccount();
        $this->createFirstUser();
        $this->stampAuthor();
        $this->log();

        return $this->user;
    }

    private function sanitize(): void
    {
        $this->firstName = TextSanitizer::plainText($this->firstName);
        $this->lastName = TextSanitizer::plainText($this->lastName);
    }

    private function createAccount(): void
    {
        $this->account = Account::query()->create([
            'name' => $this->accountName(),
        ]);
    }

    private function createFirstUser(): void
    {
        $this->user = new CreateUser(
            account: $this->account,
            email: $this->email,
            password: $this->password,
            firstName: $this->firstName,
            lastName: $this->lastName,
            role: PermissionEnum::Owner->value,
        )->execute();
    }

    private function stampAuthor(): void
    {
        $this->account->created_by_id = $this->user->id;
        $this->account->created_by_name = $this->user->getFullName();
        $this->account->updated_by_id = $this->user->id;
        $this->account->updated_by_name = $this->user->getFullName();
        $this->account->save();
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            action: UserActionEnum::AccountCreation,
        )->onQueue('low');
    }

    private function accountName(): string
    {
        $name = trim($this->firstName.' '.$this->lastName);

        return $name === '' ? 'My account' : $name;
    }
}
