<?php

namespace App\Services\Newsletter\Sender;

use App\Enums\Newsletter\NewsletterPeriodsEnum;
use App\Models\UserNewsletter;
use App\Services\Newsletter\NewsletterService;
use App\Services\User\Newsletter\UserNewsletterService;
use Illuminate\Support\Collection;

class NewsletterSender
{
    public function __construct(
        private UserNewsletterService $userNewsletterService,
        private NewsletterService $newsletterService
    ) {
    }

    public function send(NewsletterPeriodsEnum $period): void
    {
        $usersNewslettersGroupedByNewsletter = $this->userNewsletterService->getSubscribedNewslettersByPeriod($period)->groupBy('newsletter_id');
        
        /** @var Collection $newsletterGroup */
        foreach ($usersNewslettersGroupedByNewsletter as $newsletterId => $newsletterGroup) {
            $newsletter = $this->newsletterService->show($newsletterId);
            $data = $newsletter->name->getDataFetcher()->fetch($period, $newsletterGroup->pluck('user')->groupBy('id'));

            /** @var UserNewsletter $userNewsletter */
            foreach ($newsletterGroup as $userNewsletter) {
                if (!isset($data[$userNewsletter->user_id])) {
                    continue;
                }

                $userNewsletter->channel->name->getSender()->send($data[$userNewsletter->user_id],
                    $userNewsletter->user);
            }
        }
    }
}
