<?php

namespace App\Telegram\Controller\Message;

use App\Models\User;
use App\Services\Telegram\TelegramUserService;
use App\Services\Telegram\TelegramUserStateService;
use App\Telegram\Enum\State\Step\TelegramAuthStateStep;
use App\Telegram\Enum\State\TelegramState;
use App\Telegram\Facades\TgUser;
use Illuminate\Support\Facades\Hash;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Update;

class TelegramAuthController extends AbstractMessageController
{
    public function __construct(
        private TelegramUserStateService $telegramUserStateService,
        private TelegramUserService $telegramUserService
    ) {
    }

    public function email(Update $update): void
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

    public function token(Update $update): void
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
