<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\UserActionEnum;
use App\Helpers\TextSanitizer;
use App\Jobs\LogUserAction;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

/**
 * Create a user. Only an access to the instance.
 */
class CreateUser
{
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
        $this->create();
        $this->log();

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
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'trial_ends_at' => now()->addDays(30),
        ]);
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            action: UserActionEnum::AccountCreation,
        )->onQueue('low');
    }
}
