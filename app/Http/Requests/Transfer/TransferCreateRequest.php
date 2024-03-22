<?php

namespace App\Http\Requests\Transfer;

use Illuminate\Foundation\Http\FormRequest;

class TransferCreateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'account_to_id' => ['required', 'exists:accounts,id'],
            'comment' => ['nullable', 'string', 'max:255'],
            'amount_to' => ['numeric', 'required'],
            'amount_from' => ['numeric', 'required'],
            'date' => ['required', 'date'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
