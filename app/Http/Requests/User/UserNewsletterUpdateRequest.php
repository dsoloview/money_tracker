<?php

namespace App\Http\Requests\User;

use App\Enums\Newsletter\NewsletterPeriodsEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserNewsletterUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'period' => ['required', Rule::in(NewsletterPeriodsEnum::keys())],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
