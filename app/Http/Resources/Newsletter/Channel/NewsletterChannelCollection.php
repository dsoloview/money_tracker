<?php

namespace App\Http\Resources\Newsletter\Channel;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

/** @see \App\Models\Newsletter\NewsletterChannel */
class NewsletterChannelCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
        ];
    }
}
