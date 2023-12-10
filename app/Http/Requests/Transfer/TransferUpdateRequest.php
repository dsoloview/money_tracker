<?php

namespace App\Http\Requests\Transfer;

use Illuminate\Foundation\Http\FormRequest;

class TransferUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'account_from_id' => ['required', 'exists:accounts'],
            'account_to_id' => ['required', 'exists:accounts'],
            'comment' => ['nullable', 'string', 'max:255'],
            'amount' => ['required', 'integer'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
