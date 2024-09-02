<?php

namespace App\Http\Resources\Newsletter\Channel;

use App\Http\Resources\Newsletter\NewsletterCollection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Newsletter\NewsletterChannel */
class NewsletterChannelResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'availableNewsletters' => NewsletterCollection::collection($this->whenLoaded('availableNewsletters')),
        ];
    }
}
