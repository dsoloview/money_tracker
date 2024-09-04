<?php

namespace App\Enums\Newsletter;

use App\Interfaces\Newsletter\INewsletterSender;
use App\Services\Newsletter\Sender\TelegramNewsletterSender;

enum NewsletterChannelsEnum: string
{
    case TELEGRAM = 'telegram';

    public function getSender(): INewsletterSender
    {
        return match ($this) {
            self::TELEGRAM => app(TelegramNewsletterSender::class),
        };
    }
}
