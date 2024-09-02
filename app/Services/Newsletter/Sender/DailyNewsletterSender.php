<?php

namespace App\Services\Newsletter\Sender;

use App\Enums\Newsletter\NewsletterPeriodsEnum;
use App\Models\UserNewsletter;
use App\Notifications\Telegram\TelegramStatisticsNotification;
use App\Services\User\Newsletter\UserNewsletterService;

class DailyNewsletterSender
{
    public function __construct(
        private UserNewsletterService $userNewsletterService
    ) {
    }

    public function __invoke()
    {
        $usersNewsletters = $this->userNewsletterService->getNewslettersByPeriod(NewsletterPeriodsEnum::DAILY);

        /** @var UserNewsletter $userNewsletter */
        foreach ($usersNewsletters as $userNewsletter) {
            $userNewsletter->user->notify(new TelegramStatisticsNotification());
        }
    }
}
