<?php

namespace App\Http\Requests\Account;

use Illuminate\Foundation\Http\FormRequest;

class AccountRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'user_id' => ['required', 'exists:users'],
            'currency_id' => ['required', 'exists:currencies'],
            'name' => ['required', 'string', 'max:255'],
            'bank' => ['required', 'string', 'max:255'],
            'balance' => ['required', 'integer'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
