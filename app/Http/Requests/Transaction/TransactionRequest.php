<?php

namespace App\Http\Requests\Transaction;

use App\Enums\Category\CategoryTransactionTypes;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TransactionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'account_id' => ['required', 'exists:accounts'],
            'comment' => ['nullable', 'string', 'max:255'],
            'amount' => ['required', 'integer'],
            'categories_ids' => ['nullable', 'array'],
            'categories_ids.*' => ['required', 'exists:categories,id'],
            'type' => ['required', 'string', Rule::enum(CategoryTransactionTypes::class)],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
