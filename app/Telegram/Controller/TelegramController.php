<?php

namespace App\Telegram\Controller;

use App\Models\Telegram\TelegramUser;
use App\Services\Telegram\TelegramUserService;
use App\Services\Telegram\TelegramUserStateService;
use App\Telegram\Enum\AvailableTelegramCommands;
use App\Telegram\Enum\TelegramState;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Update;

readonly class TelegramController
{
    public function __construct(
        private TelegramUserService $telegramUserService,
        protected TelegramUserStateService $telegramUserStateService,
    ) {
    }

    public function process(Update $update): void
    {
        $type = $update->objectType();

        if ($type === 'callback_query') {
            $telegramUser = $this->telegramUserService->updateOrCreateTelegramUser(
                $update->getMessage()->getChat()->getId(),
                $update->getMessage()->getChat()->getId(),
                $update->getMessage()->getFrom()->getUsername()
            );
        } else {
            $telegramUser = $this->telegramUserService->updateOrCreateTelegramUser(
                $update->getMessage()->getFrom()->getId(),
                $update->getMessage()->getChat()->getId(),
                $update->getMessage()->getFrom()->getUsername()
            );
        }

        if (! $this->isUserAuthorized($telegramUser) && $this->messageShouldBeAuthorized($update, $telegramUser)) {
            $this->sendAuthorizationMessage($telegramUser);

            return;
        }

        if ($this->isCommand($update)) {
            $this->processCommand($update, $telegramUser);

            return;
        }

        if ($type === 'message') {
            $this->processMessage($update, $telegramUser);

            return;
        }

        if ($type === 'callback_query') {
            $this->processCallbackQuery($update, $telegramUser);

            return;
        }
    }

    private function isCommand(Update $update): bool
    {
        return $update->objectType() === 'message' && str_starts_with($update->getMessage()->getText(), '/');
    }

    private function isUserAuthorized(TelegramUser $telegramUser): bool
    {
        return ! is_null($telegramUser->user_id);
    }

    private function messageShouldBeAuthorized(Update $update, TelegramUser $telegramUser): bool
    {
        $text = $update->getMessage()->getText();

        $state = $telegramUser->state;

        if ($state->state === TelegramState::AUTH && ! $this->isCommand($update)) {
            return false;
        }

        if ($this->isCommand($update)
            && AvailableTelegramCommands::commandNeedToHaveAuthorization($text)) {
            return false;
        }

        return true;
    }

    private function sendAuthorizationMessage(TelegramUser $telegramUser): void
    {
        Telegram::sendMessage([
            'chat_id' => $telegramUser->chat_id,
            'text' => 'You are not authorized. Please use /authorize command to authorize.',
        ]);
    }

    private function processMessage(Update $update, TelegramUser $telegramUser): void
    {
        $messageController = new MessageController();
        $messageController->process($update, $telegramUser);
    }

    private function processCommand(Update $update, TelegramUser $telegramUser): void
    {
        $this->telegramUserStateService->resetState($telegramUser);
        Telegram::processCommand($update);
    }

    private function processCallbackQuery(Update $update, TelegramUser $telegramUser): void
    {
        $callbackQueryController = new CallbackQueryController();
        $callbackQueryController->process($update, $telegramUser);
    }
}
