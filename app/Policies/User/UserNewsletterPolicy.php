<?php

namespace App\Policies\User;

use App\Models\User;
use App\Models\UserNewsletter;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserNewsletterPolicy
{
    use HandlesAuthorization;

    public function view(User $user, UserNewsletter $userNewsletter): bool
    {
        return $user->id === $userNewsletter->user_id;
    }

    public function subscribe(User $user, UserNewsletter $userNewsletter): bool
    {
        return $user->id === $userNewsletter->user_id;
    }

    public function unsubscribe(User $user, UserNewsletter $userNewsletter): bool
    {
        return $user->id === $userNewsletter->user_id;
    }

    public function update(User $user, UserNewsletter $userNewsletter): bool
    {
        return $user->id === $userNewsletter->user_id;
    }
}
