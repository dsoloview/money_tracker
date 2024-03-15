<?php

namespace App\Http\Requests\Transaction;

use App\Enums\Category\CategoryTransactionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TransactionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'account_id' => ['required', 'exists:accounts,id'],
            'comment' => ['nullable', 'string', 'max:255'],
            'amount' => ['required', 'numeric'],
            'categories_ids' => ['nullable', 'array'],
            'categories_ids.*' => ['required', 'exists:categories,id'],
            'date' => ['required', 'date'],
            'type' => ['required', 'string', Rule::enum(CategoryTransactionType::class)],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
