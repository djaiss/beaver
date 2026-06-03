<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\User;
use PragmaRX\Google2FALaravel\Google2FA;

/**
 * Generate a QR code for 2FA setup.
 */
class Generate2faQRCode
{
    private string $secret;

    private string $qrCodeSvg;

    public function __construct(
        private readonly User $user,
    ) {}

    public function execute(): array
    {
        $this->generateSecret();
        $this->generateQRCode();
        $this->logUserAction();

        return [
            'secret' => $this->secret,
            'qrCodeSvg' => $this->qrCodeSvg,
        ];
    }

    private function generateSecret(): void
    {
        $google2fa = new Google2FA(request());

        $this->secret = $google2fa->generateSecretKey();
        $this->user->update(['two_factor_secret' => $this->secret]);
    }

    private function generateQRCode(): void
    {
        $google2fa = new Google2FA(request());

        $this->qrCodeSvg = $google2fa->getQRCodeInline(
            company: config('app.name'),
            holder: $this->user->email,
            secret: $this->secret,
            size: 120,
        );
    }

    private function logUserAction(): void
    {
        LogUserAction::dispatch(
            vault: null,
            user: $this->user,
            action: UserActionEnum::TwoFaQrCodeGeneration,
        )->onQueue('low');
    }
}
