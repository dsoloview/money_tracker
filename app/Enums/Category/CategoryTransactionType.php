<?php

namespace App\Enums\Category;

enum CategoryTransactionType: string
{
    case INCOME = 'income';
    case EXPENSE = 'expense';
    case TRANSFER = 'transfer';

    public function getTranslation(?string $languageCode = null): string
    {
        return __("default_categories.{$this->value}", [], $languageCode);
    }

    public function getCode(): int
    {
        return match ($this) {
            self::INCOME => 1,
            self::EXPENSE => 2,
            self::TRANSFER => 3,
        };
    }

    public static function fromCode(int $code): CategoryTransactionType
    {
        return match ($code) {
            1 => self::INCOME,
            2 => self::EXPENSE,
            3 => self::TRANSFER,
        };
    }

    public function isIncome(): bool
    {
        return $this === self::INCOME;
    }

    public function isExpense(): bool
    {
        return $this === self::EXPENSE;
    }

    public function isTransfer(): bool
    {
        return $this === self::TRANSFER;
    }
}
