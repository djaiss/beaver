<?php

declare(strict_types=1);

namespace App\ViewModels\Settings;

class TwoFANewViewModel
{
    public function __construct(
        private readonly array $code,
    ) {}

    public function secret(): object
    {
        return (object) [
            'secret' => $this->code['secret'],
            'qrCodeSvg' => $this->code['qrCodeSvg'],
        ];
    }

    public function url(): object
    {
        return (object) [
            'store' => route('settings.security.2fa.store'),
            'settings' => route('settings.security.index'),
        ];
    }
}
