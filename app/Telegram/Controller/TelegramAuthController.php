<?php

namespace App\Telegram\Controller;

use App\Models\Telegram\TelegramUser;
use App\Models\User;
use App\Services\Telegram\TelegramUserService;
use App\Services\Telegram\TelegramUserStateService;
use App\Telegram\Enum\TelegramAuthStateStep;
use App\Telegram\Enum\TelegramState;
use App\Telegram\Intrerface\ITelegramController;
use Illuminate\Support\Facades\Hash;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Update;

class TelegramAuthController implements ITelegramController
{
    public function __construct(
        private readonly TelegramUserStateService $telegramUserStateService,
        private readonly TelegramUserService $telegramUserService
    ) {
    }

    public function process(Update $update, TelegramUser $telegramUser): void
    {
        $step = $telegramUser->state->data['step'];

        if (empty($step)) {
            throw new \Exception('Step not found');
        }

        if ($step === TelegramAuthStateStep::EMAIL->value) {
            $this->processEmail($update, $telegramUser);

            return;
        }

        if ($step === TelegramAuthStateStep::TOKEN->value) {
            $this->processToken($update, $telegramUser);

            return;
        }
    }

    public function processEmail(Update $update, TelegramUser $telegramUser): void
    {
        $text = $update->getMessage()->getText();
        $user = User::where('email', $text)->first();

        if (! $user) {
            Telegram::sendMessage([
                'chat_id' => $telegramUser->chat_id,
                'text' => 'User not found',
            ]);

            return;
        }

        $this->telegramUserStateService->updateState($telegramUser, TelegramState::AUTH,
            [
                'email' => $text,
                'step' => TelegramAuthStateStep::TOKEN,
            ]
        );

        Telegram::sendMessage([
            'chat_id' => $update->getMessage()->getChat()->getId(),
            'text' => 'Send me your token',
        ]);
    }

    public function processToken(Update $update, TelegramUser $telegramUser): void
    {
        $text = $update->getMessage()->getText();
        $user = User::where('email', $telegramUser->state->data['email'])->first();

        if (! Hash::check($text, $user->telegramToken->token)) {
            Telegram::sendMessage([
                'chat_id' => $telegramUser->chat_id,
                'text' => 'Invalid token',
            ]);

            return;
        }

        $this->telegramUserStateService->resetState($telegramUser);
        $this->telegramUserService->authorize($telegramUser, $user);

        Telegram::sendMessage([
            'chat_id' => $update->getMessage()->getChat()->getId(),
            'text' => "You are authorized. User id: {$user->id}. Username: {$user->name}",
        ]);
    }
}
