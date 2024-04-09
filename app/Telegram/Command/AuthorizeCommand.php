<?php

namespace App\Telegram\Command;

use Telegram\Bot\Commands\Command;

class AuthorizeCommand extends Command
{
    protected string $name = 'authorize';
    protected string $description = 'Authorize Command';

    public function handle()
    {
        $this->replyWithMessage([
            'text' => 'Send me your email and password to authorize.',
        ]);
    }
}
