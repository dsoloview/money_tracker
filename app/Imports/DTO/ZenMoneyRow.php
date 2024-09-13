<?php

namespace App\Imports\DTO;

use App\Enums\Category\CategoryTransactionType;

class ZenMoneyRow
{
    private string $date;
    private ?string $categoryName;
    private ?string $payee;
    private ?string $comment;
    private ?string $outcomeAccountName;
    private ?float $outcome;
    private ?string $outcomeCurrencyShortTitle;
    private ?string $incomeAccountName;
    private ?float $income;
    private ?string $incomeCurrencyShortTitle;
    private string $createdDate;
    private string $changedDate;

    public function getDate(): string
    {
        return $this->date;
    }

    public function setDate(string $date): void
    {
        $this->date = $date;
    }

    public function getCategoryName(): ?string
    {
        return $this->categoryName;
    }

    public function setCategoryName(?string $categoryName): void
    {
        $this->categoryName = $categoryName;
    }

    public function getPayee(): ?string
    {
        return $this->payee;
    }

    public function setPayee(?string $payee): void
    {
        $this->payee = $payee;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): void
    {
        $this->comment = $comment;
    }

    public function getOutcomeAccountName(): ?string
    {
        return $this->outcomeAccountName;
    }

    public function setOutcomeAccountName(?string $outcomeAccountName): void
    {
        $this->outcomeAccountName = $outcomeAccountName;
    }

    public function getOutcome(): ?float
    {
        return $this->outcome;
    }

    public function setOutcome(?string $outcome): void
    {
        if ($outcome) {
            $outcome = str_replace(',', '.', $outcome);
            $this->outcome = (float) $outcome;
        } else {
            $this->outcome = null;
        }
    }

    public function getOutcomeCurrencyShortTitle(): ?string
    {
        return $this->outcomeCurrencyShortTitle;
    }

    public function setOutcomeCurrencyShortTitle(?string $outcomeCurrencyShortTitle): void
    {
        $this->outcomeCurrencyShortTitle = $outcomeCurrencyShortTitle;
    }

    public function getIncomeAccountName(): ?string
    {
        return $this->incomeAccountName;
    }

    public function setIncomeAccountName(?string $incomeAccountName): void
    {
        $this->incomeAccountName = $incomeAccountName;
    }

    public function getIncome(): ?float
    {
        return $this->income;
    }

    public function setIncome(?string $income): void
    {
        if ($income) {
            $income = str_replace(',', '.', $income);
            $this->income = (float) $income;
        } else {
            $this->income = null;
        }
    }

    public function getIncomeCurrencyShortTitle(): ?string
    {
        return $this->incomeCurrencyShortTitle;
    }

    public function setIncomeCurrencyShortTitle(?string $incomeCurrencyShortTitle): void
    {
        $this->incomeCurrencyShortTitle = $incomeCurrencyShortTitle;
    }

    public function getCreatedDate(): string
    {
        return $this->createdDate;
    }

    public function setCreatedDate(string $createdDate): void
    {
        $this->createdDate = $createdDate;
    }

    public function getChangedDate(): string
    {
        return $this->changedDate;
    }

    public function setChangedDate(string $changedDate): void
    {
        $this->changedDate = $changedDate;
    }

    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->setDate(trim($data['date'] ?? ''));
        $dto->setCategoryName(trim($data['categoryname'] ?? ''));
        $dto->setPayee(trim($data['payee'] ?? ''));
        $dto->setComment(trim($data['comment'] ?? ''));
        $dto->setOutcomeAccountName(trim($data['outcomeaccountname'] ?? ''));
        $dto->setOutcome(trim($data['outcome'] ?? ''));
        $dto->setOutcomeCurrencyShortTitle(trim($data['outcomecurrencyshorttitle'] ?? ''));
        $dto->setIncomeAccountName(trim($data['incomeaccountname'] ?? ''));
        $dto->setIncome(trim($data['income'] ?? ''));
        $dto->setIncomeCurrencyShortTitle(trim($data['incomecurrencyshorttitle'] ?? ''));
        $dto->setCreatedDate(trim($data['createddate'] ?? ''));
        $dto->setChangedDate(trim($data['changeddate'] ?? ''));

        return $dto;
    }

    public function getCategoryTransactionType(): CategoryTransactionType
    {
        if ($this->income !== null && $this->outcome !== null) {
            return CategoryTransactionType::TRANSFER;
        }
        if ($this->outcome !== null) {
            return CategoryTransactionType::EXPENSE;
        }

        return CategoryTransactionType::INCOME;
    }
}
