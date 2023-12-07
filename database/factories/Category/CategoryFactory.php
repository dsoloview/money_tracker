<?php

namespace Database\Factories\Category;

use App\Models\Category\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        $types = [
            'income',
            'expense',
        ];
        return [
            'parent_category_id' => Category::factory(),
            'user_id' => User::factory(),
            'icon' => $this->faker->word(),
            'name' => $this->faker->name(),
            'type' => $this->faker->randomElement($types),
            'description' => $this->faker->text(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
