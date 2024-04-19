<?php

namespace App\Enums\Category;

enum CategoryTransactionType: string
{
    case INCOME = 'income';
    case EXPENSE = 'expense';

    public function getTranslation(?string $languageCode = null): string
    {
        return __("default_categories.{$this->value}", [], $languageCode);
    }

    public function getCode(): int
    {
        return match ($this) {
            self::INCOME => 1,
            self::EXPENSE => 2,
        };
    }

    public static function fromCode(int $code): CategoryTransactionType
    {
        return match ($code) {
            1 => self::INCOME,
            2 => self::EXPENSE,
        };
    }

    public function isIncome(): bool
    {
        return $this === self::INCOME;
    }
}
