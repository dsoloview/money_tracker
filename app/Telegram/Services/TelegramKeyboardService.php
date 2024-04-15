<?php

namespace App\Telegram\Services;

use App\Telegram\DTO\CallbackQuery;
use App\Telegram\Enum\Callback\CallbackGroup;
use Telegram\Bot\Keyboard\Keyboard;

class TelegramKeyboardService
{
    public static function getTransactionsPaginationKeyboard(int $currentPage, int $totalPages): Keyboard
    {
        $buttons = [];

        if ($currentPage > 1) {
            $buttons[] = Keyboard::inlineButton([
                'text' => '🔙 Back',
                'callback_data' => CallbackQuery::buildJson(CallbackGroup::TRANSACTIONS, 'pagination',
                    ['page' => $currentPage - 1]),
            ]);
        }

        if ($currentPage < $totalPages) {
            $buttons[] = Keyboard::inlineButton([
                'text' => '🔜 Next',
                'callback_data' => CallbackQuery::buildJson(CallbackGroup::TRANSACTIONS, 'pagination',
                    ['page' => $currentPage + 1]),
            ]);
        }


        return Keyboard::make()->inline()->row($buttons)->setOneTimeKeyboard(true);
    }
}
