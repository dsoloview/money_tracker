<?php

namespace App\Services\Newsletter\DataFetcher;

use App\Data\Newsletter\StatisticsNewsletterData;
use App\Enums\Newsletter\NewsletterPeriodsEnum;
use App\Interfaces\Newsletter\INewsletterDataFetcher;
use App\Models\User;
use App\Repositories\Newsletter\NewsletterRepository;
use App\Services\Currency\CurrencyConverterService;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class StatisticsNewsletterDataFetcher implements INewsletterDataFetcher
{
    public function __construct(
        private NewsletterRepository $newsletterRepository,
        private CurrencyConverterService $currencyConverterService
    ) {
    }

    private Carbon $dateFrom;
    private Carbon $dateTo;

    public function fetch(NewsletterPeriodsEnum $period, Collection $users): array
    {
        $this->setDateFrom($period->getDateFrom());
        $this->setDateTo($period->getDateTo());

        $usersIds = $users->keys()->toArray();
        $data = $this->newsletterRepository->getStatisticsData($this->dateFrom, $this->dateTo, $usersIds);

        return $this->processData($data, $users);
    }

    private function processData(array $data, Collection $users): array
    {
        $result = [];

        foreach ($data as $row) {
            if (!$users->has($row['user_id'])) {
                continue;
            }

            $user = $users->get($row['user_id'])[0];

            if (!isset($result[$row['user_id']])) {
                $statistics = $this->getBasicStatisticsNewsletterData($user);
            } else {
                $statistics = $result[$row['user_id']];
            }

            $statistics = $this->processRow($row, $user, $statistics);

            $result[$row['user_id']] = $statistics;
        }

        return $result;
    }

    private function processRow(
        array $row,
        User $user,
        StatisticsNewsletterData $statisticsNewsletterData
    ): StatisticsNewsletterData {
        $statisticsNewsletterData->increaseTotalExpense(
            $this->currencyConverterService->convertToUserCurrency($row['total_expense'], $row['currency'],
                $user)
        );
        $statisticsNewsletterData->increaseTotalIncome(
            $this->currencyConverterService->convertToUserCurrency($row['total_income'], $row['currency'],
                $user)
        );
        $statisticsNewsletterData->increaseTransactionsCount($row['transactions_count']);
        $statisticsNewsletterData->increaseTotalBalance(
            $this->currencyConverterService->convertToUserCurrency($row['total_balance'], $row['currency'],
                $user)
        );

        return $statisticsNewsletterData;
    }

    private function getBasicStatisticsNewsletterData(User $user): StatisticsNewsletterData
    {
        return new StatisticsNewsletterData(
            userId: $user->id,
            currency: $user->currency,
            totalExpense: 0,
            totalIncome: 0,
            transactionsCount: 0,
            totalBalance: 0,
            dateFrom: $this->dateFrom,
            dateTo: $this->dateTo
        );
    }


    private function setDateFrom(Carbon $dateFrom): void
    {
        $this->dateFrom = $dateFrom;
    }

    private function setDateTo(Carbon $dateTo): void
    {
        $this->dateTo = $dateTo;
    }
}
