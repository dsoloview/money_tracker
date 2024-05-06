<?php

namespace App\Telegram\Controller\Message;

use App\Telegram\Facades\TgUser;
use App\Telegram\Intrerface\ITelegramController;
use Telegram\Bot\Objects\Update;

class AbstractMessageController implements ITelegramController
{
    public function process(Update $update): void
    {
        $step = TgUser::state()?->data['step'];

        if ($step === null) {
            throw new \Exception('Step is not defined');
        }

        $this->{$step}($update);
    }
}
