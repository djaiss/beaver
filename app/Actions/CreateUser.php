<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\PermissionEnum;
use App\Helpers\TextSanitizer;
use App\Models\Account;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

/**
 * Create a user. A user always belongs to an account, with a role.
 */
class CreateUser
{
    private User $user;

    public function __construct(
        private readonly Account $account,
        private string $email,
        private readonly string $password,
        private string $firstName,
        private string $lastName,
        private string $role = PermissionEnum::Viewer->value,
    ) {}

    public function execute(): User
    {
        $this->sanitize();
        $this->create();

        return $this->user;
    }

    private function sanitize(): void
    {
        $this->firstName = TextSanitizer::plainText($this->firstName);
        $this->lastName = TextSanitizer::plainText($this->lastName);
        $this->email = mb_strtolower(TextSanitizer::plainText($this->email));
    }

    private function create(): void
    {
        $this->user = User::query()->create([
            'account_id' => $this->account->id,
            'role' => $this->role,
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'trial_ends_at' => now()->addDays(30),
        ]);
    }
}
