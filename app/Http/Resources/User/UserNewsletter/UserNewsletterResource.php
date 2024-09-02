<?php

namespace App\Http\Resources\User\UserNewsletter;

use App\Http\Resources\Newsletter\Channel\NewsletterChannelResource;
use App\Http\Resources\Newsletter\NewsletterResource;
use App\Http\Resources\User\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\UserNewsletter */
class UserNewsletterResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'subscribed' => $this->subscribed,
            'period' => $this->period,

            'user_id' => $this->user_id,
            'newsletter_id' => $this->newsletter_id,
            'channel_id' => $this->channel_id,

            'channel' => new NewsletterChannelResource($this->whenLoaded('channel')),
            'newsletter' => new NewsletterResource($this->whenLoaded('newsletter')),
            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}
