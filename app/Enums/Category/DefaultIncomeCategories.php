<?php

namespace App\Enums\Category;

enum DefaultIncomeCategories: string
{
    case SALARY = 'salary';
    case OTHER_INCOME = 'other_income';

    public static function getType(): CategoryTransactionTypes
    {
        return CategoryTransactionTypes::INCOME;
    }

    public function getTranslation(?string $languageCode = null): string
    {
        return __("default_categories.{$this->value}", [], $languageCode);
    }
}
