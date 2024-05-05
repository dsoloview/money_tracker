<?php

namespace App\Imports\DTO;

use App\Enums\Category\CategoryTransactionType;

class ImportRow
{
    private CategoryTransactionType $type;
    private string $date;
    private ?string $categoriesNamesString;
    private ?array $categoriesNames;
    private ?string $comment;
    private string $accountName;
    private float $amount;
    private string $accountCurrencyCode;
    private ?string $transferAccountName;
    private ?float $transferAmount;
    private ?string $transferAccountCurrencyCode;

    public function getType(): CategoryTransactionType
    {
        return $this->type;
    }

    public function setType(CategoryTransactionType $type): void
    {
        $this->type = $type;
    }

    public function getDate(): string
    {
        return $this->date;
    }

    public function setDate(string $date): void
    {
        $this->date = $date;
    }

    public function getCategoriesNamesString(): ?string
    {
        return $this->categoriesNamesString;
    }

    public function setCategoriesNamesString(?string $categoriesNamesString): void
    {
        $this->categoriesNamesString = $categoriesNamesString;
    }

    public function getCategoriesNames(): ?array
    {
        return $this->categoriesNames;
    }

    public function setCategoriesNames(?array $categoriesNames): void
    {
        $this->categoriesNames = $categoriesNames;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): void
    {
        $this->comment = $comment;
    }

    public function getAccountName(): string
    {
        return $this->accountName;
    }

    public function setAccountName(string $accountName): void
    {
        $this->accountName = $accountName;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }

    public function getAccountCurrencyCode(): string
    {
        return $this->accountCurrencyCode;
    }

    public function setAccountCurrencyCode(string $accountCurrencyCode): void
    {
        $this->accountCurrencyCode = $accountCurrencyCode;
    }

    public function getTransferAccountName(): ?string
    {
        return $this->transferAccountName;
    }

    public function setTransferAccountName(?string $transferAccountName): void
    {
        $this->transferAccountName = $transferAccountName;
    }

    public function getTransferAmount(): ?float
    {
        return $this->transferAmount;
    }

    public function setTransferAmount(?float $transferAmount): void
    {
        $this->transferAmount = $transferAmount;
    }

    public function getTransferAccountCurrencyCode(): ?string
    {
        return $this->transferAccountCurrencyCode;
    }

    public function setTransferAccountCurrencyCode(?string $transferAccountCurrencyCode): void
    {
        $this->transferAccountCurrencyCode = $transferAccountCurrencyCode;
    }


}
