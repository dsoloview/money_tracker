<?php

namespace App\Telegram\Controller\Callback;

use App\Services\User\Transaction\UserTransactionService;
use App\Telegram\DTO\CallbackQuery;
use App\Telegram\Facades\TgUser;
use App\Telegram\Services\TelegramKeyboardService;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Update;

class TransactionCallbackController extends AbstractCallbackController
{
    private UserTransactionService $userTransactionService;

    protected const array AVAILABLE_TYPES = ['pagination'];

    public function __construct(UserTransactionService $userTransactionService)
    {
        $this->userTransactionService = $userTransactionService;
    }

    protected function pagination(Update $update, CallbackQuery $callbackQuery): void
    {
        $user = TgUser::user();
        $transactions = $this->userTransactionService->getUserTransactionsPaginated($user,
            $callbackQuery->data['page']);

        $currentPage = $transactions->currentPage();
        $totalPages = $transactions->lastPage();

        Telegram::editMessageText([
            'chat_id' => TgUser::chatId(),
            'message_id' => $update->getCallbackQuery()->getMessage()->getMessageId(),
            'text' => view('telegram.transactions',
                compact('transactions', 'user'))->render(),
            'parse_mode' => 'HTML',
            'reply_markup' => TelegramKeyboardService::getTransactionsPaginationKeyboard($currentPage, $totalPages)
                ->setOneTimeKeyboard(true),
        ]);
    }
}
