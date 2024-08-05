<?php

namespace App\Enums\Category;

enum DefaultIncomeCategories: string
{
    case SALARY = 'salary';
    case OTHER_INCOME = 'other_income';

    public static function getType(): CategoryTransactionType
    {
        return CategoryTransactionType::INCOME;
    }

    public function getTranslation(?string $languageCode = null): string
    {
        return __("default_categories.{$this->value}", [], $languageCode);
    }

    public static function count(): int
    {
        return count(self::cases());
    }
}
