<?php

namespace App\Telegram\Controller\Callback;

use App\Data\Account\AccountData;
use App\Services\Account\AccountService;
use App\Telegram\DTO\Callback\NewAccount\CurrencyCallbackData;
use App\Telegram\DTO\CallbackQuery;
use App\Telegram\Facades\TgUser;
use App\Telegram\Processor\TelegramNewAccountCache;
use Telegram\Bot\Objects\Update;

class NewAccountCallbackController extends AbstractCallbackController
{
    protected const array AVAILABLE_TYPES = ['currency'];

    public function __construct(
        private readonly AccountService $accountService,
    ) {
    }

    protected function currency(Update $update, CallbackQuery $callbackQuery): void
    {
        \Telegram::editMessageReplyMarkup([
            'chat_id' => TgUser::chatId(),
            'message_id' => $update->getCallbackQuery()->getMessage()->getMessageId(),
            'reply_markup' => null
        ]);

        $account = TelegramNewAccountCache::getAccountFromCache();

        $currencyCallbackData = CurrencyCallbackData::fromArray($callbackQuery->data);

        $account['currency_id'] = $currencyCallbackData->currencyId;

        $accountData = AccountData::from($account);
        $this->accountService->saveAccountForUser(TgUser::user(), $accountData);

        TelegramNewAccountCache::forgetAccountFromCache();

        \Telegram::editMessageText([
            'chat_id' => TgUser::chatId(),
            'message_id' => $update->getCallbackQuery()->getMessage()->getMessageId(),
            'text' => 'Account created',
        ]);
    }
}
