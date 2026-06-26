<?php

declare(strict_types=1);

namespace App\Http\Requests\App\Settings;

use Illuminate\Foundation\Http\FormRequest;

class ToggleAutoDeleteAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'auto_delete_account' => ['required', 'in:yes,no'],
        ];
    }
}
