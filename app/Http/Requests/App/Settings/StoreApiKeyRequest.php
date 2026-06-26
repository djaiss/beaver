<?php

declare(strict_types=1);

namespace App\Http\Requests\App\Settings;

use Illuminate\Foundation\Http\FormRequest;

class StoreApiKeyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'label' => ['required', 'string', 'min:3', 'max:255'],
        ];
    }
}
