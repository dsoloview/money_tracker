<?php

namespace App\Newsletters\Telegram;

use App\Data\Newsletter\StatisticsNewsletterData;
use App\Notifications\Telegram\AbstractTelegramNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class TelegramStatisticsNewsletter extends AbstractTelegramNotification implements ShouldQueue
{
    use Queueable;

    private StatisticsNewsletterData $data;

    public function __construct(StatisticsNewsletterData $data)
    {
        $this->data = $data;
    }

    public function toTelegram(object $notifiable): array
    {
        return [
            'text' => \Blade::render('telegram.newsletter.statistics', [
                'data' => $this->data,
            ]),
        ];
    }
}
