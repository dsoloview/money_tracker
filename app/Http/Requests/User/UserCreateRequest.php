<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UserCreateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'max:254'],
            'email' => ['required', 'email', 'max:254', 'unique:users'],
            'password' => ['required', 'confirmed', 'min:8', 'max:254'],
            'password_confirmation' => ['required'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
