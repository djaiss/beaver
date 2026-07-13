<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\EmailType;
use App\Enums\UserActionEnum;
use App\Helpers\TextSanitizer;
use App\Jobs\LogUserAction;
use App\Jobs\SendEmail;
use App\Mail\NewLoginDetected;
use App\Models\User;

/**
 * Create an API token for a successful login.
 *
 * This is deliberately separate from CreateApiKey (used for API keys the user
 * creates by hand). A login names the token after the device it came from,
 * logs the sign-in, and sends a security notification about the new sign-in —
 * never the "API key created" email.
 */
class CreateApiKeyForLogin
{
    public function __construct(
        private readonly User $user,
        private readonly ?string $deviceName = null,
    ) {}

    public function execute(): string
    {
        $device = $this->deviceLabel();

        $token = $this->user->createToken('Login from '.$device)->plainTextToken;
        $this->log();
        $this->sendEmail($device);

        return $token;
    }

    /**
     * Build a human-readable label for the device the user signed in from. It
     * names the issued token so each session is identifiable in the list of
     * personal access tokens, and it appears in the new-sign-in notification.
     */
    private function deviceLabel(): string
    {
        $deviceName = TextSanitizer::plainText((string) $this->deviceName);

        if ($deviceName === '') {
            return 'an unknown device';
        }

        return $deviceName;
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            action: UserActionEnum::ApiKeyCreation,
        )->onQueue('low');
    }

    private function sendEmail(string $device): void
    {
        SendEmail::dispatch(
            mailable: new NewLoginDetected(
                device: $device,
            ),
            user: $this->user,
            emailType: EmailType::NewLogin,
        )->onQueue('high');
    }
}
