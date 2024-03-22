<?php

namespace Database\Factories\Transfer;

use App\Models\Account\Account;
use App\Models\Transfer\Transfer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class TransferFactory extends Factory
{
    protected $model = Transfer::class;

    public function definition(): array
    {
        return [
            'account_from_id' => Account::factory(),
            'account_to_id' => Account::factory(),
            'comment' => $this->faker->word(),
            'amount_from' => $this->faker->randomFloat(2),
            'amount_to' => $this->faker->randomFloat(2),
            'date' => $this->faker->date(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
