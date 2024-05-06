<?php

namespace App\Telegram\Controller\Message;

use App\Services\Telegram\TelegramUserStateService;
use App\Services\Transaction\TelegramTransactionService;
use App\Services\Transaction\TransactionService;
use App\Telegram\Enum\State\Step\TelegramNewTransactionStateStep;
use App\Telegram\Enum\State\TelegramState;
use App\Telegram\Facades\TgUser;
use App\Telegram\Services\Transaction\TransactionMessageService;
use Carbon\Carbon;
use Telegram\Bot\Objects\Update;

class NewTransactionController extends AbstractMessageController
{
    private int $transactionId;

    public function __construct(
        private TransactionService $transactionService,
        private TelegramTransactionService $telegramTransactionService,
        private TelegramUserStateService $telegramUserStateService,
        private TransactionMessageService $transactionMessageService
    ) {
    }

    public function process(Update $update): void
    {
        $this->transactionId = TgUser::state()?->data['transactionId'];
        parent::process($update);
    }

    public function amount(Update $update): void
    {
        $amount = $update->getMessage()->getText();
        $this->validateAmount($amount);

        $this->telegramTransactionService->setTransactionAmount($this->transactionId, $amount);

        $this->telegramUserStateService->updateState(TgUser::get(),
            TelegramState::NEW_TRANSACTION,
            [
                'step' => TelegramNewTransactionStateStep::COMMENT->value,
                'transactionId' => $this->transactionId,
            ]
        );

        $this->transactionMessageService->sendCommentMessage();
    }

    private function validateAmount(string $amount): void
    {
        if (!is_numeric($amount)) {
            throw new \Exception('Amount must be a number');
        }

        if ($amount <= 0) {
            throw new \Exception('Amount must be greater than 0');
        }
    }

    public function comment(Update $update): void
    {
        $comment = $update->getMessage()->getText();
        $this->telegramTransactionService->setTransactionComment($this->transactionId, $comment);

        $this->telegramUserStateService->updateState(TgUser::get(),
            TelegramState::NEW_TRANSACTION,
            [
                'step' => TelegramNewTransactionStateStep::DATE->value,
                'transactionId' => $this->transactionId,
            ]
        );

        $this->transactionMessageService->sendDateMessage();
    }

    public function date(Update $update): void
    {
        $date = $update->getMessage()->getText();
        $date = Carbon::parse($date);

        $this->telegramTransactionService->setTransactionDate($this->transactionId, $date);

        $this->telegramUserStateService->resetState(TgUser::get());

        $transaction = $this->telegramTransactionService->finishTransaction($this->transactionId);
        $this->transactionService->syncAccountBalanceForNewTransaction($transaction, $transaction->account);

        $this->transactionMessageService->sendTransactionMessage($transaction);
    }
}
