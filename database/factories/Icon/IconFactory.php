<?php

namespace Database\Factories\Icon;

use App\Models\Icon\Icon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class IconFactory extends Factory
{
    protected $model = Icon::class;

    public function definition(): array
    {
        return [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'path' => $this->faker->filePath(),
            'name' => $this->faker->name(),
        ];
    }
}
