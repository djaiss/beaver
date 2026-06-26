<?php

declare(strict_types=1);

namespace App\ViewModels\Settings;

class ApiKeyCreateViewModel
{
    public function url(): object
    {
        return (object) [
            'store' => route('settings.api-keys.store'),
            'settings' => route('settings.security.index'),
        ];
    }
}
