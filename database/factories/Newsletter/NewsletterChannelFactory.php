<?php

namespace Database\Factories\Newsletter;

use App\Models\Newsletter\NewsletterChannel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class NewsletterChannelFactory extends Factory
{
    protected $model = NewsletterChannel::class;

    public function definition(): array
    {
        return [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'name' => $this->faker->name(),
        ];
    }
}
