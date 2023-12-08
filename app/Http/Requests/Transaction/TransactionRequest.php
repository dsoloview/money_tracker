<?php

namespace App\Http\Requests\Transaction;

use Illuminate\Foundation\Http\FormRequest;

class TransactionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'account_id' => ['required', 'exists:accounts'],
            'comment' => ['nullable', 'string', 'max:255'],
            'amount' => ['required', 'integer'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
