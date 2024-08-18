<?php

namespace Tests\Traits;

use App\Models\User;

trait WithTestUser
{
    protected User $user;

//    protected function setUp(): void
//    {
//        parent::setUp();
//
////        $this->user = User::factory()->hasSettings()->create();
//    }

    protected function setUpTestUser(): void
    {
        $this->user = User::factory()->hasSettings()->create();
    }
}
