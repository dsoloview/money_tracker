<?php

namespace Database\Factories\Category;

use App\Enums\Category\CategoryTransactionType;
use App\Models\Category\Category;
use App\Models\Icon\Icon;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        $types = [CategoryTransactionType::EXPENSE, CategoryTransactionType::INCOME];
        return [
            'name' => $this->faker->name(),
            'description' => $this->faker->text(),
            'type' => $types[array_rand($types)],
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'user_id' => User::factory(),
            'icon_id' => Icon::factory(),
        ];
    }
}
