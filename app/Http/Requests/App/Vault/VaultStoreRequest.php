<?php

declare(strict_types=1);

namespace App\Http\Requests\App\Vault;

use Illuminate\Foundation\Http\FormRequest;

class VaultStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'vault_name' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-zA-Z0-9\s\-_]+$/',
            ],
        ];
    }
}
