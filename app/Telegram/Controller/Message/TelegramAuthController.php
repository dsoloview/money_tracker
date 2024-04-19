<?php

namespace App\Telegram\Controller\Message;

use App\Models\User;
use App\Services\Telegram\TelegramUserService;
use App\Services\Telegram\TelegramUserStateService;
use App\Telegram\Enum\State\Step\TelegramAuthStateStep;
use App\Telegram\Enum\State\TelegramState;
use App\Telegram\Facades\TgUser;
use App\Telegram\Intrerface\ITelegramController;
use Illuminate\Support\Facades\Hash;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Update;

readonly class TelegramAuthController implements ITelegramController
{
    public function __construct(
        private TelegramUserStateService $telegramUserStateService,
        private TelegramUserService $telegramUserService
    ) {
    }

    public function process(Update $update): void
    {
        $step = TgUser::state()?->data['step'];

        if (empty($step)) {
            throw new \Exception('Step not found');
        }

        if ($step === TelegramAuthStateStep::EMAIL->value) {
            $this->processEmail($update);

            return;
        }

        if ($step === TelegramAuthStateStep::TOKEN->value) {
            $this->processToken($update);

            return;
        }
    }

    public function processEmail(Update $update): void
    {
        $text = $update->getMessage()->getText();
        $user = User::where('email', $text)->first();

        if (!$user) {
            Telegram::sendMessage([
                'chat_id' => TgUser::chatId(),
                'text' => 'User not found',
            ]);

            return;
        }

        $this->telegramUserStateService->updateState(TgUser::get(), TelegramState::AUTH,
            [
                'email' => $text,
                'step' => TelegramAuthStateStep::TOKEN,
            ]
        );

        Telegram::sendMessage([
            'chat_id' => TgUser::chatId(),
            'text' => 'Send me your token',
        ]);
    }

    public function processToken(Update $update): void
    {
        $text = $update->getMessage()->getText();
        $user = User::where('email', TgUser::state()?->data['email'])->first();

        if (!Hash::check($text, $user->telegramToken->token)) {
            Telegram::sendMessage([
                'chat_id' => TgUser::chatId(),
                'text' => 'Invalid token',
            ]);

            return;
        }

        $this->telegramUserStateService->resetState(TgUser::get());
        $this->telegramUserService->authorize(TgUser::get(), $user);

        Telegram::sendMessage([
            'chat_id' => TgUser::chatId(),
            'text' => "You are authorized. User id: {$user->id}. Username: {$user->name}",
        ]);
    }
}
