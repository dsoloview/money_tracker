<?php

namespace App\Services\Newsletter;

use App\Models\Newsletter\Newsletter;
use Illuminate\Support\Collection;

class NewsletterService
{
    public function getAll(): Collection
    {
        return \Cache::rememberForever('newsletters', function () {
            return Newsletter::with('availableChannels')->get();
        });
    }

    public function show(int $id): Newsletter
    {
        return \Cache::rememberForever("newsletter_{$id}", function () use ($id) {
            return Newsletter::with('availableChannels')->findOrFail($id);
        });
    }
}
