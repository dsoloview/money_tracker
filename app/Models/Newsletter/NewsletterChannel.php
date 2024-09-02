<?php

namespace App\Models\Newsletter;

use App\Enums\Newsletter\NewsletterChannelsEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class NewsletterChannel extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'name' => NewsletterChannelsEnum::class
        ];
    }

    public function availableNewsletters(): BelongsToMany
    {
        return $this->belongsToMany(Newsletter::class, 'newsletters_available_channels', 'channel_id', 'newsletter_id');
    }
}
