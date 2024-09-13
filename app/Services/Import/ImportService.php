<?php

namespace App\Services\Import;

use App\Data\Account\AccountData;
use App\Data\Category\CategoryData;
use App\Data\Transaction\TransactionData;
use App\Data\Transfer\TransferData;
use App\Imports\DTO\ImportRow;
use App\Models\Account\Account;
use App\Models\User;
use App\Services\Account\AccountService;
use App\Services\Category\CategoryService;
use App\Services\Currency\CurrencyService;
use App\Services\Transaction\TransactionService;
use App\Services\Transfer\TransferService;
use App\Services\User\UserService;
use App\Telegram\Enum\Import\ImportMode;
use Illuminate\Support\Collection;

class ImportService
{
    private ImportRow $row;
    private ImportMode $importMode;
    private User $user;

    public function __construct(
        private readonly TransferService $transferService,
        private readonly TransactionService $transactionService,
        private readonly AccountService $accountService,
        private readonly CurrencyService $currencyService,
        private readonly CategoryService $categoryService,
        private readonly UserService $userService,
    ) {
    }

    public function importRow(ImportRow $importRow, ImportMode $importMode, User $user): void
    {
        $this->row = $importRow;
        $this->importMode = $importMode;
        $this->user = $user;
        $this->processRow();
    }

    private function processRow(): void
    {
        if ($this->row->getType()->isTransfer()) {
            $this->processTransferRow();
        } else {
            $this->processTransactionRow();
        }
    }

    private function processTransferRow(): void
    {
        $transferAccounts = $this->importMode->isCreateAbsentEntities()
            ? $this->getOrCreateTransferAccounts()
            : $this->getTransferAccountsIfExists();

        $incomeAccount = $transferAccounts['income'];
        $outcomeAccount = $transferAccounts['outcome'];
        if ($incomeAccount === null || $outcomeAccount === null) {
            return;
        }
        $transferData = new TransferData(
            $incomeAccount['id'],
            $this->row->getComment(),
            $this->row->getAmount(),
            $this->row->getTransferAmount(),
            $this->row->getDate()
        );

        $this->transferService->createTransfer($outcomeAccount, $transferData);
    }

    private function processTransactionRow(): void
    {
        $account = $this->importMode->isCreateAbsentEntities()
            ? $this->getOrCreateAccount()
            : $this->getAccountIfExists();

        $categories = $this->importMode->isCreateAbsentEntities()
            ? $this->getOrCreateCategories()
            : $this->getCategoriesIfExists();

        if ($account === null || $categories->isEmpty()) {
            return;
        }

        $transactionData = new TransactionData(
            $this->row->getComment(),
            $this->row->getAmount(),
            $categories->pluck('id')->toArray(),
            $this->row->getType(),
            $this->row->getDate()
        );

        $this->transactionService->createTransactionForAccount($account, $transactionData);
    }

    private function getAccountIfExists(): ?Account
    {
        $accountName = $this->row->getAccountName();
        $accountCurrencyCode = $this->row->getAccountCurrencyCode();

        return \Cache::tags(["import_{$this->user->id}"])->remember("import_account_{$accountName}_{$this->user->id}_{$accountCurrencyCode}",
            now()->addMinute(), function () use ($accountName, $accountCurrencyCode) {
                return $this->accountService->getAccountByNameUserAndCurrencyCode($accountName, $this->user,
                    $accountCurrencyCode);
            });
    }

    private function getOrCreateAccount(): Account
    {
        $account = $this->getAccountIfExists();
        if ($account === null) {
            $currency = $this->currencyService->getCurrencyByCode($this->row->getAccountCurrencyCode());
            if ($currency === null) {
                throw new \Exception('Currency not found '.$this->row->getAccountCurrencyCode());
            }
            $accountName = $this->row->getAccountName();

            $accountData = new AccountData(
                $currency->id,
                0,
                $accountName,
                $accountName,
            );

            $account = $this->accountService->saveAccountForUser($this->user, $accountData);
            \Cache::delete("import_account_{$accountName}_{$this->user->id}_{$currency->code}");
        }

        return $account;
    }

    private function getTransferAccountsIfExists(): Collection
    {
        $incomeAccount = \Cache::tags(["import_{$this->user->id}"])->remember("import_account_{$this->row->getTransferAccountName()}_{$this->user->id}_{$this->row->getTransferAccountCurrencyCode()}",
            now()->addMinute(), function () {
                return $this->accountService->getAccountByNameUserAndCurrencyCode(
                    $this->row->getTransferAccountName(),
                    $this->user,
                    $this->row->getTransferAccountCurrencyCode()
                );
            });
        $outcomeAccount = \Cache::tags(["import_{$this->user->id}"])->remember("import_account_{$this->row->getAccountName()}_{$this->user->id}_{$this->row->getAccountCurrencyCode()}",
            now()->addMinute(), function () {
                return $this->accountService->getAccountByNameUserAndCurrencyCode(
                    $this->row->getAccountName(),
                    $this->user,
                    $this->row->getAccountCurrencyCode()
                );
            });

        return collect([
            'income' => $incomeAccount,
            'outcome' => $outcomeAccount,
        ]);
    }

    private function getOrCreateTransferAccounts(): Collection
    {
        $accounts = $this->getTransferAccountsIfExists();
        $incomeAccount = $accounts['income'];
        $outcomeAccount = $accounts['outcome'];

        if (!$incomeAccount) {
            $incomeCurrency = $this->currencyService->getCurrencyByCode($this->row->getTransferAccountCurrencyCode());
            if (!$incomeCurrency) {
                throw new \Exception('Income currency not found '.$this->row->getTransferAccountName());
            }
            $incomeAccountData = new AccountData(
                $incomeCurrency->id,
                0,
                $this->row->getTransferAccountName(),
                $this->row->getTransferAccountName(),
            );

            $incomeAccount = $this->accountService->saveAccountForUser($this->user, $incomeAccountData);
            \Cache::delete("import_account_{$this->row->getTransferAccountName()}_{$this->user->id}_{$this->row->getTransferAccountCurrencyCode()}");
        }

        if (!$outcomeAccount) {
            $outcomeCurrency = $this->currencyService->getCurrencyByCode($this->row->getAccountCurrencyCode());
            if (!$outcomeCurrency) {
                throw new \Exception('Outcome currency not found '.$this->row->getAccountCurrencyCode());
            }
            $outcomeAccountData = new AccountData(
                $outcomeCurrency->id,
                0,
                $this->row->getAccountName(),
                $this->row->getAccountName(),
            );

            $outcomeAccount = $this->accountService->saveAccountForUser($this->user, $outcomeAccountData);
            \Cache::delete("import_account_{$this->row->getAccountName()}_{$this->user->id}_{$this->row->getAccountCurrencyCode()}");
        }

        return collect([
            'income' => $incomeAccount,
            'outcome' => $outcomeAccount,
        ]);

    }

    private function getCategoriesIfExists(): Collection
    {
        $categoriesNames = $this->row->getCategoriesNames();

        $cache = \Cache::get("import_categories_{$this->row->getCategoriesNamesString()}_{$this->user->id}");

        if ($cache) {
            return $cache;
        }

        $categories = $this->categoryService->getUsersCategoriesByNamesAndType($this->user, $categoriesNames,
            $this->row->getType());

        \Cache::put("import_categories_{$this->row->getCategoriesNamesString()}_{$this->user->id}", $categories,
            now()->addMinute());

        return $categories;
    }

    private function getOrCreateCategories(): Collection
    {
        $categories = $this->getCategoriesIfExists();
        $categoriesNames = $this->row->getCategoriesNames();

        $categoriesNames = collect($categoriesNames)->map(fn($name) => trim($name));
        $categoriesNames = $categoriesNames->diff($categories->pluck('name'));

        if ($categoriesNames->isEmpty()) {
            return $categories;
        }

        $categoriesNames = $categoriesNames->map(fn($name) => new CategoryData(
            null,
            null,
            $this->row->getType()->value,
            $name,
            null,
        ));

        \Cache::delete("import_categories_{$this->row->getCategoriesNamesString()}_{$this->user->id}");

        return $categories->merge($categoriesNames->map(fn(CategoryData $data
        ) => $this->userService->createUsersCategory($this->user, $data)));
    }
}
