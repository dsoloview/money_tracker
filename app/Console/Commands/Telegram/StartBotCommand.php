<?php

namespace App\Console\Commands\Telegram;

use App\Telegram\Controller\TelegramController;
use Illuminate\Console\Command;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Update;

class StartBotCommand extends Command
{
    protected $signature = 'telegram:start-bot';

    protected $description = 'Command description';

    public function handle(): void
    {
        $updateId = -1;
        $telegramController = app(TelegramController::class);

        while (true) {
            /** @var Update[] $response */
            $response = Telegram::getUpdates([
                'offset' => $updateId + 1
            ]);

            foreach ($response as $update) {
                $telegramController->process($update);

                $updateId = $update->updateId;
            }
        }
    }
}
