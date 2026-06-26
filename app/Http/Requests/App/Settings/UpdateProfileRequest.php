<?php

declare(strict_types=1);

namespace App\Http\Requests\App\Settings;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'nickname' => ['nullable', 'string', 'max:100'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
                'disposable_email',
            ],
            'locale' => ['required', 'string', 'max:5', Rule::in(config('app.supported_locales'))],
            'time_format_24h' => ['required', Rule::in(['true', 'false'])],
        ];
    }
}
