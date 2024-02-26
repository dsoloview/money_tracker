<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required'],
            'email' => ['required', 'email', 'max:254', Rule::unique('users')->ignore($this->user ?? null)],
            'settings' => ['required', 'array'],
            'settings.language_id' => ['required', 'exists:languages,id'],
            'settings.main_currency_id' => ['required', 'exists:currencies,id'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
