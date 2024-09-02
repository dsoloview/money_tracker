<?php

namespace App\Models;

use App\Enums\Newsletter\NewsletterPeriodsEnum;
use App\Models\Newsletter\Newsletter;
use App\Models\Newsletter\NewsletterChannel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserNewsletter extends Model
{
    protected $table = 'users_newsletters';
    public $timestamps = false;

    protected $fillable = [
        'period',
        'subscribed_at',
        'unsubscribed_at',
    ];

    protected $casts = [
        'subscribed_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
        'period' => NewsletterPeriodsEnum::class
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function newsletter(): BelongsTo
    {
        return $this->belongsTo(Newsletter::class);
    }

    public function channel(): BelongsTo
    {
        return $this->belongsTo(NewsletterChannel::class);
    }
}
