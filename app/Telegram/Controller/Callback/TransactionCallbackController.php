<?php

namespace App\Telegram\Controller\Callback;

use App\Models\Telegram\TelegramUser;
use App\Services\User\Transaction\UserTransactionService;
use App\Telegram\DTO\CallbackQuery;
use App\Telegram\Services\TelegramKeyboardService;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Update;

class TransactionCallbackController extends AbstractCallbackController
{
    private UserTransactionService $userTransactionService;
    protected const AVAILABLE_TYPES = ['pagination'];

    public function __construct(UserTransactionService $userTransactionService)
    {
        $this->userTransactionService = $userTransactionService;
    }

    protected function pagination(Update $update, TelegramUser $telegramUser, CallbackQuery $callbackQuery): void
    {
        $transactions = $this->userTransactionService->getUserTransactionsPaginated($telegramUser->user,
            $callbackQuery->data['page']);

        $currentPage = $transactions->currentPage();
        $totalPages = $transactions->lastPage();

        $user = $telegramUser->user;

        Telegram::editMessageText([
            'chat_id' => $telegramUser->chat_id,
            'message_id' => $update->getCallbackQuery()->getMessage()->getMessageId(),
            'text' => view('telegram.transactions',
                compact('transactions', 'user'))->render(),
            'parse_mode' => 'HTML',
            'reply_markup' => TelegramKeyboardService::getTransactionsPaginationKeyboard($currentPage, $totalPages)
                ->setOneTimeKeyboard(true),
        ]);
    }
}
