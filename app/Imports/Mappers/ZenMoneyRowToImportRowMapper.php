<?php

namespace App\Imports\Mappers;

use App\Imports\DTO\ImportRow;
use App\Imports\DTO\ZenMoneyRow;

class ZenMoneyRowToImportRowMapper
{
    public static function map(ZenMoneyRow $zenMoneyRow): ImportRow
    {
        $importRow = new ImportRow();
        $importRow->setDate($zenMoneyRow->getDate());
        $importRow->setComment($zenMoneyRow->getComment());
        $importRow->setType($zenMoneyRow->getCategoryTransactionType());
        $importRow->setCategoriesNames(self::mapCategories($zenMoneyRow->getCategoryName()));
        $importRow->setCategoriesNamesString($zenMoneyRow->getCategoryName());
        $importRow = self::setDataByType($zenMoneyRow, $importRow);

        return $importRow;
    }

    private static function setDataByType(ZenMoneyRow $zenMoneyRow, ImportRow $importRow): ImportRow
    {
        if ($importRow->getType()->isIncome()) {
            $importRow->setAccountName($zenMoneyRow->getIncomeAccountName());
            $importRow->setAmount($zenMoneyRow->getIncome());
            $importRow->setAccountCurrencyCode($zenMoneyRow->getIncomeCurrencyShortTitle());
        }

        if ($importRow->getType()->isExpense()) {
            $importRow->setAccountName($zenMoneyRow->getOutcomeAccountName());
            $importRow->setAmount($zenMoneyRow->getOutcome());
            $importRow->setAccountCurrencyCode($zenMoneyRow->getOutcomeCurrencyShortTitle());
        }

        if ($importRow->getType()->isTransfer()) {
            $importRow->setAccountName($zenMoneyRow->getOutcomeAccountName());
            $importRow->setAmount($zenMoneyRow->getOutcome());
            $importRow->setAccountCurrencyCode($zenMoneyRow->getOutcomeCurrencyShortTitle());
            $importRow->setTransferAccountName($zenMoneyRow->getIncomeAccountName());
            $importRow->setTransferAmount($zenMoneyRow->getIncome());
            $importRow->setTransferAccountCurrencyCode($zenMoneyRow->getIncomeCurrencyShortTitle());
        }

        return $importRow;
    }

    private static function mapCategories(?string $categoryName): array
    {
        if ($categoryName === null) {
            return [];
        }
        
        $categories = explode(', ', $categoryName);

        return array_map(function ($category) {
            return trim($category);
        }, $categories);
    }
}
