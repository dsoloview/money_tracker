<?php

namespace App\Enums\Category;

enum CategoryTypes: string
{
    case INCOME = 'income';
    case EXPENSE = 'expense';

    public function getTranslation(string $languageCode = null): string
    {
        return __("default_categories.{$this->value}", [], $languageCode);
    }
}
