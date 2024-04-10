<?php

namespace App\Telegram\Controller;

use App\Models\Telegram\TelegramUser;
use App\Services\Telegram\TelegramUserService;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Update;

readonly class TelegramController
{
    public function __construct(
        private TelegramUserService $telegramUserService
    ) {
    }

    public function process(Update $update): void
    {
        $type = $update->objectType();

        $telegramUser = $this->telegramUserService->updateOrCreateTelegramUser(
            $update->getMessage()->getFrom()->getId(),
            $update->getMessage()->getChat()->getId(),
            $update->getMessage()->getFrom()->getUsername()
        );

        if ($type === 'message' && str_starts_with($update->getMessage()->getText(), '/')) {
            $this->processCommand($update);

            return;
        }

        if ($type === 'message') {
            $this->processMessage($update, $telegramUser);

            return;
        }

    }

    private function processMessage(Update $update, TelegramUser $telegramUser): void
    {
        $messageController = new MessageController();
        $messageController->process($update, $telegramUser);
    }

    private function processCommand(Update $update): void
    {
        Telegram::processCommand($update);
    }
}
