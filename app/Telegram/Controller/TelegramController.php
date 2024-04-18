<?php

namespace App\Telegram\Controller;

use App\Models\Telegram\TelegramUser;
use App\Services\Telegram\TelegramUserStateService;
use App\Telegram\Enum\AvailableTelegramCommands;
use App\Telegram\Enum\TelegramState;
use App\Telegram\Facades\TgUser;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Update;

readonly class TelegramController
{
    public function __construct(
        protected TelegramUserStateService $telegramUserStateService,
    ) {
    }

    public function process(Update $update): void
    {
        try {
            $type = $update->objectType();

            $telegramUser = $this->getTelegramUser($update, $type);

            if (!TgUser::isAuthorized() && $this->messageShouldBeAuthorized($update, $telegramUser)) {
                $this->sendAuthorizationMessage();

                return;
            }

            if ($this->isCommand($update)) {
                $this->processCommand($update);

                return;
            }

            if ($type === 'message') {
                $this->processMessage($update);

                return;
            }

            if ($type === 'callback_query') {
                $this->processCallbackQuery($update);

                return;
            }
        } catch (\Throwable $exception) {
            Telegram::sendMessage([
                'chat_id' => TgUser::chatId(),
                'text' => $exception->getMessage(),
            ]);
        }
    }

    private function getTelegramUser(Update $update, string $type): TelegramUser
    {
        if ($type === 'callback_query') {
            $telegramUser = TgUser::updateOrCreateTelegramUser(
                $update->getMessage()->getChat()->getId(),
                $update->getMessage()->getChat()->getId(),
                $update->getMessage()->getFrom()->getUsername()
            );
        } else {
            $telegramUser = TgUser::updateOrCreateTelegramUser(
                $update->getMessage()->getFrom()->getId(),
                $update->getMessage()->getChat()->getId(),
                $update->getMessage()->getFrom()->getUsername()
            );
        }

        return $telegramUser;
    }

    private function isCommand(Update $update): bool
    {
        return $update->objectType() === 'message' && str_starts_with($update->getMessage()->getText(), '/');
    }

    private function messageShouldBeAuthorized(Update $update, TelegramUser $telegramUser): bool
    {
        $text = $update->getMessage()->getText();

        $state = $telegramUser->state;

        if ($state?->state === TelegramState::AUTH && !$this->isCommand($update)) {
            return false;
        }

        if ($this->isCommand($update)
            && AvailableTelegramCommands::commandNeedToHaveAuthorization($text)) {
            return false;
        }

        return true;
    }

    private function sendAuthorizationMessage(): void
    {
        Telegram::sendMessage([
            'chat_id' => TgUser::get()->chat_id,
            'text' => 'You are not authorized. Please use /authorize command to authorize.',
        ]);
    }

    private function processMessage(Update $update): void
    {
        $messageController = new MessageController();
        $messageController->process($update);
    }

    private function processCommand(Update $update): void
    {
        $this->telegramUserStateService->resetState(TgUser::get());
        Telegram::processCommand($update);
    }

    private function processCallbackQuery(Update $update): void
    {
        $callbackQueryController = new CallbackQueryController();
        $callbackQueryController->process($update);
    }
}
