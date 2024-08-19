<?php

namespace Tests\Traits;

use App\Models\User;

trait WithTestUser
{
    protected User $user;

    protected function setUpTestUser(): void
    {
        $this->user = User::factory()->hasSettings()->create();
    }

    protected function createTestUser(): User
    {
        return User::factory()->hasSettings()->create();
    }
}
