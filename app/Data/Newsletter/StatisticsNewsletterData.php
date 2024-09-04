<?php

namespace App\Data\Newsletter;

use App\Interfaces\Newsletter\INewsletterData;
use App\Models\Currency\Currency;
use Carbon\Carbon;
use Spatie\LaravelData\Data;

class StatisticsNewsletterData extends Data implements INewsletterData
{
    public function __construct(
        public int $userId,
        public Currency $currency,
        public float $totalExpense,
        public float $totalIncome,
        public int $transactionsCount,
        public float $totalBalance,
        public Carbon $dateFrom,
        public Carbon $dateTo,
    ) {
    }

    public function setTotalExpense(float $totalExpense): void
    {
        $this->totalExpense = round($totalExpense, 2);
    }

    public function increaseTotalExpense(float $amount): void
    {
        $result = round($this->totalExpense + $amount, 2);
        $this->setTotalExpense($result);
    }

    public function setTotalIncome(float $totalIncome): void
    {
        $this->totalIncome = round($totalIncome, 2);
    }

    public function increaseTotalIncome(float $amount): void
    {
        $result = round($this->totalIncome + $amount, 2);
        $this->setTotalIncome($result);
    }

    public function setTransactionsCount(int $transactionsCount): void
    {
        $this->transactionsCount = $transactionsCount;
    }

    public function increaseTransactionsCount(int $count = 1): void
    {
        $this->transactionsCount += $count;
    }

    public function setTotalBalance(float $totalBalance): void
    {
        $this->totalBalance = round($totalBalance, 2);
    }

    public function increaseTotalBalance(float $amount): void
    {
        $result = round($this->totalBalance + $amount, 2);
        $this->setTotalBalance($result);
    }
}
