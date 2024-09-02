<?php

namespace App\Http\Resources\Newsletter;

use App\Http\Resources\Newsletter\Channel\NewsletterChannelResource;
use App\Http\Resources\User\UserCollection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Newsletter\Newsletter */
class NewsletterResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'availablePeriods' => $this->available_periods,
            'availableChannels' => NewsletterChannelResource::collection($this->whenLoaded('availableChannels')),
            'users' => UserCollection::collection($this->whenLoaded('users')),
        ];
    }
}
