<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\PermissionEnum;
use App\Models\User;

/**
 * Register a brand new user. The user creates an account first, of which they
 * are the owner.
 */
class SignUp
{
    public function __construct(
        private readonly string $email,
        private readonly string $password,
        private readonly string $firstName,
        private readonly string $lastName,
    ) {}

    public function execute(): User
    {
        $user = new CreateUser(
            email: $this->email,
            password: $this->password,
            firstName: $this->firstName,
            lastName: $this->lastName,
        )->execute();

        $account = new CreateAccount(
            author: $user,
            name: $this->accountName(),
        )->execute();

        new AddAccountMember(
            account: $account,
            user: $user,
            role: PermissionEnum::Owner->value,
        )->execute();

        return $user;
    }

    private function accountName(): string
    {
        $name = trim($this->firstName.' '.$this->lastName);

        return $name === '' ? 'My account' : $name;
    }
}
