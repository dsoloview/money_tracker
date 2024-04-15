<?php

namespace App\Telegram\Controller;

use App\Models\Telegram\TelegramUser;
use App\Services\User\Transaction\UserTransactionService;
use App\Telegram\Intrerface\ITelegramController;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Update;

class CallbackQueryController implements ITelegramController
{
    public function process(Update $update, TelegramUser $telegramUser): void
    {
        $userTransactionsService = app(UserTransactionService::class);
        $data = $update->getCallbackQuery()->getData();

        $page = (int) explode('_', $data)[2];

        $transactions = $userTransactionsService->getUserTransactionsPaginated($telegramUser->user, $page);

        $user = $telegramUser->user;

        Telegram::deleteMessage([
            'chat_id' => $telegramUser->chat_id,
            'message_id' => $update->getCallbackQuery()->getMessage()->getMessageId(),
        ]);
        
        Telegram::sendMessage([
            'chat_id' => $telegramUser->chat_id,
            'text' => view('telegram.transactions',
                compact('transactions', 'user'))->render(),
            'parse_mode' => 'HTML',
            'reply_markup' => Keyboard::make()->inline()->row([
                Keyboard::inlineButton([
                    'text' => '🔙 Back',
                    'callback_data' => "transactions_page_".($page - 1),
                ]),
                Keyboard::inlineButton([
                    'text' => '🔜 Next',
                    'callback_data' => "transactions_page_".($page + 1),
                ]),
            ])->setOneTimeKeyboard(true)
        ]);
    }
}
