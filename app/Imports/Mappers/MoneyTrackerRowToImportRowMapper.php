<?php

namespace App\Imports\Mappers;

use App\Imports\DTO\ImportRow;
use App\Imports\DTO\MoneyTrackerRow;

class MoneyTrackerRowToImportRowMapper
{
    public static function map(MoneyTrackerRow $moneyTrackerRow): ImportRow
    {
        $importRow = new ImportRow();
        $importRow->setDate($moneyTrackerRow->getDate());
        $importRow->setComment($moneyTrackerRow->getComment());
        $importRow->setType($moneyTrackerRow->getType());
        $importRow->setCategoriesNames(self::mapCategories($moneyTrackerRow->getCategories()));
        $importRow->setCategoriesNamesString($moneyTrackerRow->getCategories());
        $importRow = self::setDataByType($moneyTrackerRow, $importRow);

        return $importRow;
    }

    private static function setDataByType(MoneyTrackerRow $zenMoneyRow, ImportRow $importRow): ImportRow
    {
        $importRow->setAccountName($zenMoneyRow->getAccount());
        $importRow->setAmount($zenMoneyRow->getAmount());
        $importRow->setAccountCurrencyCode($zenMoneyRow->getCurrency());

        if ($importRow->getType()->isTransfer()) {
            $importRow->setTransferAccountName($zenMoneyRow->getTransferAccount());
            $importRow->setTransferAmount($zenMoneyRow->getTransferAmount());
            $importRow->setTransferAccountCurrencyCode($zenMoneyRow->getTransferCurrency());
        }

        return $importRow;
    }

    private static function mapCategories(?string $categoriesNames): array
    {
        if ($categoriesNames === null) {
            return [];
        }

        $categories = explode(', ', $categoriesNames);

        return array_map(function ($category) {
            return trim($category);
        }, $categories);
    }
}
