<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'parent_category_id' => ['nullable', 'int', 'exists:categories,id'],
            'icon_id' => ['nullable', 'integer', 'exists:icons,id'],
            'name' => ['required', 'max:255'],
            'description' => ['nullable', 'max:255'],
            'type' => ['required', 'string'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
