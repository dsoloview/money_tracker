<?php

namespace App\Services\Newsletter\Sender;

use App\Interfaces\Newsletter\INewsletterData;
use App\Interfaces\Newsletter\INewsletterSender;
use App\Models\User;
use App\Newsletters\Telegram\TelegramStatisticsNewsletter;

class TelegramNewsletterSender implements INewsletterSender
{
    public function send(INewsletterData $data, User $user): void
    {
        if ($user->isAuthorizedInTelegram()) {
            $user->notify(new TelegramStatisticsNewsletter($data));
        }
    }
}
