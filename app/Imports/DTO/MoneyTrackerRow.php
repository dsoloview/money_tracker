<?php

namespace App\Imports\DTO;

use App\Enums\Category\CategoryTransactionType;

class MoneyTrackerRow
{
    private string $date;
    private CategoryTransactionType $type;
    private ?string $categories;
    private string $account;
    private float $amount;
    private string $currency;
    private ?string $comment;
    private ?string $transferAccount;
    private ?float $transferAmount;
    private ?string $transferCurrency;

    public function getDate(): string
    {
        return $this->date;
    }

    public function setDate(string $date): void
    {
        $this->date = $date;
    }

    public function getType(): CategoryTransactionType
    {
        return $this->type;
    }

    public function setType(CategoryTransactionType $type): void
    {
        $this->type = $type;
    }

    public function getCategories(): ?string
    {
        return $this->categories;
    }

    public function setCategories(?string $categories): void
    {
        $this->categories = $categories;
    }

    public function getAccount(): string
    {
        return $this->account;
    }

    public function setAccount(string $account): void
    {
        $this->account = $account;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(?string $amount): void
    {
        $amount = str_replace(',', '.', $amount);
        $this->amount = (float) $amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): void
    {
        $this->comment = $comment;
    }

    public function getTransferAccount(): ?string
    {
        return $this->transferAccount;
    }

    public function setTransferAccount(?string $transferAccount): void
    {
        $this->transferAccount = $transferAccount;
    }

    public function getTransferAmount(): ?float
    {
        return $this->transferAmount;
    }

    public function setTransferAmount(?string $transferAmount): void
    {
        if ($transferAmount) {
            $transferAmount = str_replace(',', '.', $transferAmount);
            $this->transferAmount = (float) $transferAmount;
        } else {
            $this->transferAmount = null;
        }
    }

    public function getTransferCurrency(): ?string
    {
        return $this->transferCurrency;
    }

    public function setTransferCurrency(?string $transferCurrency): void
    {
        $this->transferCurrency = $transferCurrency;
    }
    
    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->setDate($data['date']);
        $dto->setType(CategoryTransactionType::from($data['type']));
        $dto->setCategories($data['categories']);
        $dto->setAccount($data['account']);
        $dto->setAmount($data['amount']);
        $dto->setCurrency($data['currency']);
        $dto->setComment($data['comment']);
        $dto->setTransferAccount($data['transfer_account']);
        $dto->setTransferAmount($data['transfer_amount']);
        $dto->setTransferCurrency($data['transfer_currency']);

        return $dto;
    }
}
