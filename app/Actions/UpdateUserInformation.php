<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\UserActionEnum;
use App\Helpers\TextSanitizer;
use App\Jobs\LogUserAction;
use App\Models\User;
use Illuminate\Auth\Events\Registered;

class UpdateUserInformation
{
    public function __construct(
        private readonly User $user,
        private readonly string $email,
        private string $firstName,
        private string $lastName,
        private ?string $nickname,
        private string $locale,
        private readonly bool $timeFormat24h,
    ) {}

    /**
     * Update the user information.
     * If the email has changed, we need to send a new verification email to
     * verify the new email address.
     */
    public function execute(): User
    {
        $this->sanitize();
        $emailChanged = $this->user->email !== $this->email;
        $this->update();

        if ($emailChanged) {
            $this->triggerEmailVerification();
        }
        $this->log();

        return $this->user;
    }

    private function sanitize(): void
    {
        $this->firstName = TextSanitizer::plainText($this->firstName);
        $this->lastName = TextSanitizer::plainText($this->lastName);
        $this->nickname = TextSanitizer::nullablePlainText($this->nickname);
        $this->locale = TextSanitizer::plainText($this->locale);
    }

    private function triggerEmailVerification(): void
    {
        $this->user->email_verified_at = null;
        $this->user->save();
        event(new Registered($this->user));
    }

    private function update(): void
    {
        $this->user->update([
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'email' => $this->email,
            'nickname' => $this->nickname,
            'locale' => $this->locale,
            'time_format_24h' => $this->timeFormat24h,
        ]);
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            vault: null,
            user: $this->user,
            action: UserActionEnum::PersonalProfileUpdate,
        )->onQueue('low');
    }
}
