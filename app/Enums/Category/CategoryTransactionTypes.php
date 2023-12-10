<?php

namespace App\Enums\Category;

enum CategoryTransactionTypes: string
{
    case INCOME = 'income';
    case EXPENSE = 'expense';

    public function getTranslation(?string $languageCode = null): string
    {
        return __("default_categories.{$this->value}", [], $languageCode);
    }

    public function isIncome(): bool
    {
        return $this === self::INCOME;
    }
}
