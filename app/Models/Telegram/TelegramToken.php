<?php

namespace App\Models\Telegram;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TelegramToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'token',
        'user_id',
    ];

    protected $hidden = [
        'token',
    ];

    protected function casts(): array
    {
        return [
            'token' => 'hashed',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
