<?php

namespace App\Telegram\Controller\Message;

use App\Services\Telegram\TelegramUserStateService;
use App\Services\Transaction\TelegramTransactionService;
use App\Services\Transaction\TransactionService;
use App\Telegram\Enum\State\Step\TelegramNewTransactionStateStep;
use App\Telegram\Enum\State\TelegramState;
use App\Telegram\Facades\TgUser;
use App\Telegram\Intrerface\ITelegramController;
use App\Telegram\Services\Transaction\TransactionMessageService;
use Carbon\Carbon;
use Telegram\Bot\Objects\Update;

readonly class NewTransactionController implements ITelegramController
{
    public function __construct(
        private TransactionService $transactionService,
        private TelegramTransactionService $telegramTransactionService,
        private TelegramUserStateService $telegramUserStateService,
        private TransactionMessageService $transactionMessageService
    ) {
    }

    public function process(Update $update): void
    {
        $transactionId = TgUser::state()?->data['transactionId'];
        $step = TgUser::state()?->data['step'];

        if (empty($transactionId)) {
            throw new \Exception('Step not found');
        }

        if ($step === TelegramNewTransactionStateStep::AMOUNT->value) {
            $this->processAmount($update, $transactionId);

            return;
        }

        if ($step === TelegramNewTransactionStateStep::COMMENT->value) {
            $this->processComment($update, $transactionId);

            return;
        }

        if ($step === TelegramNewTransactionStateStep::DATE->value) {
            $this->processDate($update, $transactionId);

            return;
        }
    }

    public function processAmount(Update $update, int $transactionId): void
    {
        $amount = $update->getMessage()->getText();
        $this->validateAmount($amount);

        $this->telegramTransactionService->setTransactionAmount($transactionId, $amount);

        $this->telegramUserStateService->updateState(TgUser::get(),
            TelegramState::NEW_TRANSACTION,
            [
                'step' => TelegramNewTransactionStateStep::COMMENT->value,
                'transactionId' => $transactionId
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

    public function processComment(Update $update, int $transactionId): void
    {
        $comment = $update->getMessage()->getText();
        $this->telegramTransactionService->setTransactionComment($transactionId, $comment);

        $this->telegramUserStateService->updateState(TgUser::get(),
            TelegramState::NEW_TRANSACTION,
            [
                'step' => TelegramNewTransactionStateStep::DATE->value,
                'transactionId' => $transactionId
            ]
        );

        $this->transactionMessageService->sendDateMessage();
    }

    public function processDate(Update $update, int $transactionId): void
    {
        $date = $update->getMessage()->getText();
        $date = Carbon::parse($date);

        $this->telegramTransactionService->setTransactionDate($transactionId, $date);

        $this->telegramUserStateService->resetState(TgUser::get());

        $transaction = $this->telegramTransactionService->finishTransaction($transactionId);
        $this->transactionService->syncAccountBalanceForNewTransaction($transaction);

        $this->transactionMessageService->sendTransactionMessage($transaction);
    }
}
