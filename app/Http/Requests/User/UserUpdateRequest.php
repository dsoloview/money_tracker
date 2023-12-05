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
            'password' => ['exclude_if:password,null'],
            'password_confirmation' => ['required_unless:password,null'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
