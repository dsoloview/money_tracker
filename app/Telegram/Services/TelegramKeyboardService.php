<?php

namespace App\Telegram\Services;

use App\Enums\Category\CategoryTransactionType;
use App\Enums\Import\ImportFormat;
use App\Models\Currency\Currency;
use App\Telegram\DTO\CallbackQuery;
use App\Telegram\Enum\Callback\Account\CallbackNewAccountGroupType;
use App\Telegram\Enum\Callback\CallbackGroup;
use App\Telegram\Enum\Callback\Transaction\CallbackNewTransactionGroupType;
use App\Telegram\Enum\Callback\Transaction\CallbackTransactionGroupType;
use App\Telegram\Enum\Import\ImportMode;
use Illuminate\Support\Collection;
use Telegram\Bot\Keyboard\Keyboard;

class TelegramKeyboardService
{
    public static function getTransactionsPaginationKeyboard(int $currentPage, int $totalPages): Keyboard
    {
        $buttons = [];

        if ($currentPage > 1) {
            $buttons[] = Keyboard::inlineButton([
                'text' => '🔙 Back',
                'callback_data' => CallbackQuery::buildJson(CallbackGroup::TRANSACTIONS,
                    CallbackTransactionGroupType::PAGINATION->value,
                    ['page' => $currentPage - 1]),
            ]);
        }

        if ($currentPage < $totalPages) {
            $buttons[] = Keyboard::inlineButton([
                'text' => '🔜 Next',
                'callback_data' => CallbackQuery::buildJson(CallbackGroup::TRANSACTIONS,
                    CallbackTransactionGroupType::PAGINATION->value,
                    ['page' => $currentPage + 1]),
            ]);
        }

        return Keyboard::make()->inline()->row($buttons)->setOneTimeKeyboard(true);
    }

    public static function getAccountsKeyboard(Collection $accounts): Keyboard
    {
        $keyboard = Keyboard::make()->inline();

        foreach ($accounts as $account) {
            $keyboard->row([
                Keyboard::inlineButton([
                    'text' => $account->name.' '.$account->balance.' '.$account->currency->symbol,
                    'callback_data' => CallbackQuery::buildJson(CallbackGroup::NEW_TRANSACTION,
                        CallbackNewTransactionGroupType::ACCOUNT->value,
                        [$account->id]),
                ]),
            ]);
        }

        return $keyboard->setOneTimeKeyboard(true)->setResizeKeyboard(true);
    }

    public static function getTransactionTypesKeyboard(int $transactionId): Keyboard
    {
        $buttons = [];

        $buttons[] = Keyboard::inlineButton([
            'text' => 'Income',
            'callback_data' => CallbackQuery::buildJson(CallbackGroup::NEW_TRANSACTION,
                CallbackNewTransactionGroupType::TYPE->value,
                [CategoryTransactionType::INCOME->getCode(), $transactionId]),
        ]);

        $buttons[] = Keyboard::inlineButton([
            'text' => 'Expense',
            'callback_data' => CallbackQuery::buildJson(CallbackGroup::NEW_TRANSACTION,
                CallbackNewTransactionGroupType::TYPE->value,
                [CategoryTransactionType::EXPENSE->getCode(), $transactionId]),
        ]);

        return Keyboard::make()->inline()->row($buttons)->setOneTimeKeyboard(true);
    }

    public static function getCategoriesKeyboard(Collection $categories, int $transactionId): Keyboard
    {
        $keyboard = Keyboard::make()->inline();

        foreach ($categories->chunk(3) as $chunk) {
            $row = [];
            foreach ($chunk as $category) {
                $row[] = Keyboard::inlineButton([
                    'text' => $category['isSelected'] ? '✅ '.$category['name'] : $category['name'],
                    'callback_data' => CallbackQuery::buildJson(CallbackGroup::NEW_TRANSACTION,
                        CallbackNewTransactionGroupType::CATEGORY->value, [
                            $category['isSelected'] ? '-'.$category['id'] : $category['id'],
                            $transactionId,
                        ]),
                ]);
            }

            $keyboard->row($row);
        }

        $doneButton = Keyboard::inlineButton([
            'text' => '🔚 Done',
            'callback_data' => CallbackQuery::buildJson(CallbackGroup::NEW_TRANSACTION,
                CallbackNewTransactionGroupType::CATEGORY_DONE->value, [$transactionId]),
        ]);

        $keyboard->row([$doneButton]);

        return $keyboard->setOneTimeKeyboard(true)->setResizeKeyboard(true);
    }

    public static function getCurrenciesKeyboard(Collection $currencies): Keyboard
    {
        $keyboard = Keyboard::make()->inline();

        foreach ($currencies->chunk(3) as $chunk) {
            $row = [];
            /** @var  Currency $currency */
            foreach ($chunk as $currency) {
                $row[] = Keyboard::inlineButton([
                    'text' => $currency->code.' '.$currency->symbol,
                    'callback_data' => CallbackQuery::buildJson(CallbackGroup::NEW_ACCOUNT,
                        CallbackNewAccountGroupType::CURRENCY->value, [$currency->id]),
                ]);
            }

            $keyboard->row($row);
        }

        return $keyboard->setOneTimeKeyboard(true)->setResizeKeyboard(true);
    }

    public static function getImportModeKeyboard(): Keyboard
    {
        $buttons = [];

        $buttons[] = Keyboard::button([
            'text' => ImportMode::CREATE_ABSENT_ENTITIES->value,
        ]);

        $buttons[] = Keyboard::inlineButton([
            'text' => ImportMode::NOT_CREATE_ABSENT_ENTITIES->value,
        ]);

        return Keyboard::make()->row($buttons)->setOneTimeKeyboard(true);
    }

    public static function getImportFormatKeyboard(): Keyboard
    {
        $buttons = [];

        foreach (ImportFormat::cases() as $importFormat) {
            $buttons[] = Keyboard::button([
                'text' => $importFormat->value,
            ]);
        }

        return Keyboard::make()->row($buttons)->setOneTimeKeyboard(true);
    }
}
