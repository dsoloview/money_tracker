<?php

namespace App\Telegram\Services\Account;

use App\Telegram\Facades\TgUser;
use App\Telegram\Services\TelegramKeyboardService;
use Illuminate\Support\Collection;

readonly class AccountMessageService
{
    public function sendAccountNameMessage(): void
    {
        \Telegram::sendMessage([
            'chat_id' => TgUser::chatId(),
            'text' => 'Please enter the name of the account',
        ]);
    }

    public function sendAccountBankMessage(): void
    {
        \Telegram::sendMessage([
            'chat_id' => TgUser::chatId(),
            'text' => 'Please enter the name of the bank',
        ]);
    }

    public function sendAccountBalanceMessage(): void
    {
        \Telegram::sendMessage([
            'chat_id' => TgUser::chatId(),
            'text' => 'Please enter the balance of the account',
        ]);
    }

    public function sendAccountCurrencyMessage(Collection $currencies): void
    {
        \Telegram::sendMessage([
            'chat_id' => TgUser::chatId(),
            'text' => 'Please enter the currency of the account',
            'reply_markup' => TelegramKeyboardService::getCurrenciesKeyboard($currencies),
        ]);
    }
}
