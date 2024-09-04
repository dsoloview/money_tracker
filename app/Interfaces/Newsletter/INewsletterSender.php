<?php

namespace App\Interfaces\Newsletter;

use App\Models\User;

interface INewsletterSender
{
    public function send(INewsletterData $data, User $user): void;
}
