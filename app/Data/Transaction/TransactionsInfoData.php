<?php

namespace App\Data\Transaction;

use App\Http\Resources\Currency\CurrencyResource;
use App\Models\Currency\Currency;
use Spatie\LaravelData\Data;

class TransactionsInfoData extends Data
{
    public CurrencyResource $currency;

    public float $total_expense;

    public float $total_income;

    public float $min_transaction;

    public float $max_transaction;

    public function __construct(
        Currency $currency,
        float $total_expense,
        float $total_income,
        float $min_transaction,
        float $max_transaction,
    ) {
        $this->currency = new CurrencyResource($currency);
        $this->total_expense = $total_expense;
        $this->total_income = $total_income;
        $this->min_transaction = $min_transaction;
        $this->max_transaction = $max_transaction;
    }

    public function setCurrency(Currency $currency): void
    {
        $this->currency = new CurrencyResource($currency);
    }

    public function setTotalExpense(float $total_expense): void
    {
        $this->total_expense = round($total_expense, 2);
    }

    public function increaseTotalExpense(float $amount): void
    {
        $result = round($this->total_expense + $amount, 2);
        $this->setTotalExpense($result);
    }

    public function setTotalIncome(float $total_income): void
    {
        $this->total_income = round($total_income, 2);
    }

    public function increaseTotalIncome(float $amount): void
    {
        $result = round($this->total_income + $amount, 2);
        $this->setTotalIncome($result);
    }

    public function setMinTransaction(float $min_transaction): void
    {
        $this->min_transaction = round($min_transaction, 2);
    }

    public function updateMinTransaction(float $amount): void
    {
        $result = round(min($this->min_transaction, $amount), 2);
        $this->setMinTransaction($result);
    }

    public function setMaxTransaction(float $max_transaction): void
    {
        $this->max_transaction = round($max_transaction, 2);
    }

    public function updateMaxTransaction(float $amount): void
    {
        $result = round(max($this->max_transaction, $amount), 2);
        $this->setMaxTransaction($result);
    }
}
