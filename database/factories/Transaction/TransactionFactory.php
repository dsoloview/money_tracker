<?php

namespace Database\Factories\Transaction;

use App\Enums\Category\CategoryTransactionTypes;
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
            'type' => $this->faker->randomElement([CategoryTransactionTypes::INCOME, CategoryTransactionTypes::EXPENSE]),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'account_id' => Account::factory(),
        ];
    }

    public function configure(): TransactionFactory
    {
        return $this->afterCreating(function (Transaction $transaction) {
            $rand = rand(0, 1);

            if ($rand) {
                $rand = rand(1, 3);
                for ($i = 0; $i < $rand; $i++) {
                    $transaction->categories()->save(Category::factory()->make());
                }
                $transaction->categories()->save(Category::factory()->make());
            }

        });
    }
}
