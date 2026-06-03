<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\EmailType;
use App\Enums\UserActionEnum;
use App\Helpers\TextSanitizer;
use App\Jobs\LogUserAction;
use App\Jobs\SendEmail;
use App\Mail\ApiKeyCreated;
use App\Models\User;
use Illuminate\Validation\ValidationException;

/**
 * Create an API key for the user, at the instance level. The key will have
 * the permissions the user have.
 */
class CreateApiKey
{
    public function __construct(
        private readonly User $user,
        private string $label,
    ) {}

    public function execute(): string
    {
        $this->sanitize();
        $this->validate();

        $token = $this->user->createToken($this->label)->plainTextToken;
        $this->log();
        $this->sendEmail();

        return $token;
    }

    private function sanitize(): void
    {
        $this->label = TextSanitizer::plainText($this->label);
    }

    private function validate(): void
    {
        if ($this->label === '') {
            throw ValidationException::withMessages([
                'label' => 'API key label must be plain text.',
            ]);
        }
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            vault: null,
            user: $this->user,
            action: UserActionEnum::ApiKeyCreation,
            description: 'Created an API key',
        )->onQueue('low');
    }

    private function sendEmail(): void
    {
        SendEmail::dispatch(
            mailable: new ApiKeyCreated(
                label: $this->label,
            ),
            user: $this->user,
            emailType: EmailType::ApiCreated,
        )->onQueue('high');
    }
}
