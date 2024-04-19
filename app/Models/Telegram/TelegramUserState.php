<?php

namespace App\Models\Telegram;

use App\Telegram\Enum\State\TelegramState;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TelegramUserState extends Model
{
    use HasFactory;

    protected $fillable = [
        'telegram_user_id',
        'state',
        'data',
    ];

    public function casts(): array
    {
        return [
            'state' => TelegramState::class,
            'data' => 'array',
        ];
    }

    public function telegramUser(): BelongsTo
    {
        return $this->belongsTo(TelegramUser::class, 'telegram_user_id', 'telegram_id');
    }
}
