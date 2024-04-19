<?php

namespace App\Telegram\Controller\Callback;

use App\Models\Category\Category;
use App\Services\Category\CategoryService;
use App\Services\Telegram\TelegramUserStateService;
use App\Services\Transaction\TelegramTransactionService;
use App\Services\Transaction\TransactionService;
use App\Telegram\DTO\Callback\NewTransaction\AccountCallbackData;
use App\Telegram\DTO\Callback\NewTransaction\CategoryCallbackData;
use App\Telegram\DTO\Callback\NewTransaction\CategoryDoneCallbackData;
use App\Telegram\DTO\Callback\NewTransaction\TransactionTypeCallbackData;
use App\Telegram\DTO\CallbackQuery;
use App\Telegram\Enum\State\Step\TelegramNewTransactionStateStep;
use App\Telegram\Enum\State\TelegramState;
use App\Telegram\Facades\TgUser;
use App\Telegram\Services\Transaction\TransactionMessageService;
use Telegram\Bot\Objects\Update;

class NewTransactionCallbackController extends AbstractCallbackController
{
    protected const array AVAILABLE_TYPES = ['type', 'category', 'account', 'categoryDone'];

    public function __construct(
        private readonly CategoryService $categoryService,
        private readonly TelegramUserStateService $telegramUserStateService,
        private readonly TransactionMessageService $transactionMessageService,
        private readonly TelegramTransactionService $telegramTransactionService,
        private readonly TransactionService $transactionService,
    ) {
    }

    protected function account(Update $update, CallbackQuery $callbackQuery): void
    {
        $callbackData = AccountCallbackData::fromArray($callbackQuery->data);

        $transaction = $this->telegramTransactionService->createTelegramTransaction($callbackData->accountId);

        $this->transactionMessageService->editCallbackMessage(
            $update->getCallbackQuery()->getMessage()->getMessageId(),
            'Chosen account: '.$transaction->account->name
        );

        $this->transactionMessageService->sendTransactionTypesMessage(TgUser::get(), $transaction->id);
    }

    protected function type(Update $update, CallbackQuery $callbackQuery): void
    {
        $callbackData = TransactionTypeCallbackData::fromArray($callbackQuery->data);

        $this->telegramTransactionService->setTransactionType($callbackData->transactionId,
            $callbackData->type);

        $categories = $this->categoryService->getUsersCategoriesByType(TgUser::user(), $callbackData->type);

        $categories = $categories->map(function (Category $category) {
            $category->isSelected = false;

            return $category;
        });

        $this->transactionMessageService->editCallbackMessage(
            $update->getCallbackQuery()->getMessage()->getMessageId(),
            'Chosen type: '.$callbackData->type->value
        );

        $this->transactionMessageService->sendTransactionCategoriesMessage(TgUser::get(), $categories,
            $callbackData->transactionId, $update->getCallbackQuery()->getMessage()->getMessageId());
    }

    protected function category(Update $update, CallbackQuery $callbackQuery): void
    {
        $callbackData = CategoryCallbackData::fromArray($callbackQuery->data);

        if ($callbackData->categoryId > 0) {
            $this->telegramTransactionService->addTransactionCategory($callbackData->transactionId,
                $callbackData->categoryId);
        } else {
            $this->telegramTransactionService->removeTransactionCategory($callbackData->transactionId,
                abs($callbackData->categoryId));
        }

        $transaction = $this->transactionService->getTransactionById($callbackData->transactionId);
        $transactionCategories = $transaction->categories->pluck('id')->toArray();

        $categories = $this->categoryService->getUsersCategoriesByType(TgUser::user(), $transaction->type);

        $categories = $categories->map(function (Category $category) use ($transactionCategories) {
            $category->isSelected = in_array($category->id, $transactionCategories);

            return $category;
        });

        $this->transactionMessageService->sendTransactionCategoriesMessage(TgUser::get(), $categories,
            $callbackData->transactionId, $update->getCallbackQuery()->getMessage()->getMessageId());
    }

    protected function categoryDone(Update $update, CallbackQuery $callbackQuery): void
    {
        $callbackData = CategoryDoneCallbackData::fromArray($callbackQuery->data);

        $this->telegramUserStateService->updateOrCreateStateByTelegramId(TgUser::telegramId(),
            TelegramState::NEW_TRANSACTION,
            [
                'step' => TelegramNewTransactionStateStep::AMOUNT->value,
                'transactionId' => $callbackData->transactionId,
            ]
        );

        $this->transactionMessageService->editCallbackMessage(
            $update->getCallbackQuery()->getMessage()->getMessageId(),
            'Categories chosen:'.PHP_EOL.$this->transactionService->getTransactionById($callbackData->transactionId)->categories->pluck('name')->implode(', ')
        );

        $this->transactionMessageService->sendTransactionAmountMessage(TgUser::get());
    }


}
