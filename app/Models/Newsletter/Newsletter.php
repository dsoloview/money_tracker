<?php

namespace App\Models\Newsletter;

use App\Enums\Newsletter\NewsletterPeriodsEnum;
use App\Enums\Newsletter\NewslettersEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Newsletter extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'name' => NewslettersEnum::class
        ];
    }

    public function availableChannels(): BelongsToMany
    {
        return $this->belongsToMany(NewsletterChannel::class, 'newsletters_available_channels', 'newsletter_id',
            'channel_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'users_newsletters', 'newsletter_id', 'user_id');
    }

    public function getAvailablePeriodsAttribute(): array
    {
        return NewsletterPeriodsEnum::toArrayWithTranslations();
    }
}
