<?php

namespace App\Services\Newsletter;

use App\Models\Newsletter\NewsletterChannel;
use Illuminate\Support\Collection;

class NewsletterChannelsService
{
    public function getAll(): Collection
    {
        return \Cache::rememberForever('newsletter_channels', function () {
            return NewsletterChannel::all();
        });
    }

    public function show(int $id): NewsletterChannel
    {
        return \Cache::rememberForever("newsletter_channel_{$id}", function () use ($id) {
            return NewsletterChannel::findOrFail($id);
        });
    }
}
