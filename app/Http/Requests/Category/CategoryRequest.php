<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'parent_category_id' => ['nullable', 'int', 'exists:categories,id'],
            'user_id' => ['required', 'int', 'exists:users,id'],
            'icon' => ['nullable', 'max:255'],
            'name' => ['required', 'max:255'],
            'description' => ['nullable', 'max:255'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
