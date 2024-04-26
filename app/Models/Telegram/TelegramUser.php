<?php

namespace App\Models\Telegram;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;

class TelegramUser extends Model
{
    use HasFactory;
    use Notifiable;

    protected $fillable = [
        'telegram_id',
        'username',
        'user_id',
        'chat_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function state(): HasOne
    {
        return $this->hasOne(TelegramUserState::class, 'telegram_user_id', 'telegram_id');
    }

    public function isAuthorized(): bool
    {
        return $this->user_id !== null;
    }

    public function routeNotificationFor($driver, $notification = null)
    {
        return $this->chat_id;
    }
}
