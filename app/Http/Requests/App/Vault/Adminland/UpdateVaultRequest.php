<?php

declare(strict_types=1);

namespace App\Http\Requests\App\Vault\Adminland;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVaultRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, list<string>>
     */
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
