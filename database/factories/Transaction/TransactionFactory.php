<?php

namespace Database\Factories\Transaction;

use App\Models\Account\Account;
use App\Models\Category\Category;
use App\Models\Transaction\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition(): array
    {
        return [
            'comment' => $this->faker->word(),
            'amount' => $this->faker->randomFloat(2),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'account_id' => Account::factory(),
        ];
    }

    public function configure(): TransactionFactory
    {
        return $this->afterCreating(function (Transaction $transaction) {
            $transaction->categories()->save(Category::factory()->make());
        });
    }
}
