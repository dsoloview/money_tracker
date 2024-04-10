<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateSettingsRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'language_id' => ['required', 'exists:languages,id'],
            'main_currency_id' => ['required', 'exists:currencies,id'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
