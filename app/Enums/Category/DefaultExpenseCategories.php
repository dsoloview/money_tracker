<?php

namespace App\Enums\Category;

enum DefaultExpenseCategories: string
{
    case HOME = 'home';
    case FOOD = 'food';
    case TRANSPORT = 'transport';
    case ENTERTAINMENT = 'entertainment';
    case HEALTH = 'health';
    case CLOTHES = 'clothes';
    case OTHER_EXPENSE = 'other_expense';

    public function getType(): CategoryTypes
    {
        return CategoryTypes::EXPENSE;
    }

    public function getTranslation(string $languageCode = null): string
    {
        return __("default_categories.{$this->value}", [], $languageCode);
    }
}
