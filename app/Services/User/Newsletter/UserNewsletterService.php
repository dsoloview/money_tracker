<?php

namespace App\Services\User\Newsletter;

use App\Data\User\Newsletter\UserNewsletterUpdateData;
use App\Enums\Newsletter\NewsletterChannelsEnum;
use App\Enums\Newsletter\NewsletterPeriodsEnum;
use App\Models\Newsletter\NewsletterChannel;
use App\Models\User;
use App\Models\UserNewsletter;
use App\Services\Newsletter\NewsletterService;
use Illuminate\Support\Collection;

class UserNewsletterService
{
    public function __construct(
        private NewsletterService $newsletterService
    ) {
    }

    public function getUserNewsletters(User $user): Collection
    {
        $usersNewslettersIds = UserNewsletter::where('user_id', $user->id)->pluck('newsletter_id');
        $otherNewsletters = $this->newsletterService->getAll()->filter(function ($newsletter) use ($usersNewslettersIds
        ) {
            return !$usersNewslettersIds->contains($newsletter->id);
        });

        if ($otherNewsletters->isNotEmpty()) {
            $otherNewsletters->each(function ($newsletter) use ($user) {
                UserNewsletter::create([
                    'user_id' => $user->id,
                    'newsletter_id' => $newsletter->id,
                    'channel_id' => NewsletterChannel::where('name', NewsletterChannelsEnum::TELEGRAM)->first()->id,
                ]);
            });
        }

        return UserNewsletter::with('newsletter', 'newsletter.availableChannels', 'channel')
            ->where('user_id', $user->id)->get();
    }

    public function getNewslettersByPeriod(NewsletterPeriodsEnum $period): Collection
    {
        return UserNewsletter::with('user')->where('period', $period)->get();
    }

    public function createNewslettersForUser(User $user): void
    {
        $newsletters = $this->newsletterService->getAll();
        foreach ($newsletters as $newsletter) {
            UserNewsletter::updateOrCreate([
                'user_id' => $user->id,
                'newsletter_id' => $newsletter->id,
                'channel_id' => $newsletter->availableChannels->first()->id,
            ], [
                'user_id' => $user->id,
                'newsletter_id' => $newsletter->id,
            ]);
        }
    }

    public function subscribe(UserNewsletter $userNewsletter): void
    {
        UserNewsletter::where('id', $userNewsletter->id)->update([
            'subscribed' => true,
            'subscribed_at' => now(),
            'unsubscribed_at' => null,
        ]);
    }

    public function unsubscribe(UserNewsletter $userNewsletter): void
    {
        UserNewsletter::where('id', $userNewsletter->id)->update([
            'subscribed' => false,
            'unsubscribed_at' => now(),
            'subscribed_at' => null,
        ]);
    }

    public function update(UserNewsletter $userNewsletter, UserNewsletterUpdateData $data): void
    {
        $userNewsletter->update($data->all());
    }
}
