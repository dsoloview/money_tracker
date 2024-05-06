<?php

namespace App\Telegram\Controller\Message;

use App\Exports\MoneyTrackerExport;
use App\Jobs\Export\TelegramExportJob;
use App\Services\Telegram\TelegramUserStateService;
use App\Telegram\Enum\State\Step\TelegramExportStep;
use App\Telegram\Enum\State\TelegramState;
use App\Telegram\Facades\TgUser;
use Carbon\Carbon;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Objects\Update;

class ExportController extends AbstractMessageController
{
    public function __construct(
        private readonly TelegramUserStateService $telegramUserStateService
    ) {
    }

    protected function dateFrom(Update $update): void
    {
        $dateFrom = $update->getMessage()->getText();

        $this->telegramUserStateService->updateOrCreateStateByTelegramId(
            $update->getMessage()->getFrom()->getId(),
            TelegramState::EXPORT,
            [
                'step' => TelegramExportStep::DATE_TO->value,
                'date_from' => $dateFrom
            ]
        );

        \Telegram::sendMessage([
            'chat_id' => TgUser::chatId(),
            'text' => 'Please enter the end date in the format YYYY-MM-DD',
        ]);
    }

    protected function dateTo(Update $update): void
    {
        $dateTo = $update->getMessage()->getText();
        $dateFrom = TgUser::state()?->data['date_from'];

        $this->telegramUserStateService->updateOrCreateStateByTelegramId(
            $update->getMessage()->getFrom()->getId(),
            TelegramState::EXPORT,
            [
                'step' => TelegramExportStep::CONFIRMATION->value,
                'date_from' => $dateFrom,
                'date_to' => $dateTo
            ]
        );

        \Telegram::sendMessage([
            'chat_id' => TgUser::chatId(),
            'text' => "You want to export data from $dateFrom to $dateTo. Please confirm.",
            'reply_markup' => Keyboard::make()->row([
                Keyboard::button(['text' => 'Yes']),
                Keyboard::button(['text' => 'No']),
            ])->setOneTimeKeyboard(true)
        ]);
    }

    protected function confirmation(Update $update): void
    {
        $confirmation = $update->getMessage()->getText();
        $dateFrom = TgUser::state()?->data['date_from'];
        $dateTo = TgUser::state()?->data['date_to'];

        if ($confirmation === 'No') {
            $this->telegramUserStateService->updateOrCreateStateByTelegramId(
                $update->getMessage()->getFrom()->getId(),
                TelegramState::EXPORT,
                [
                    'step' => TelegramExportStep::DATE_FROM->value
                ]
            );

            \Telegram::sendMessage([
                'chat_id' => TgUser::chatId(),
                'text' => 'Please enter the start date in the format YYYY-MM-DD',
            ]);

            return;
        }

        if ($confirmation === 'Yes') {
            \Telegram::sendMessage([
                'chat_id' => TgUser::chatId(),
                'text' => "Exporting data from $dateFrom to $dateTo",
            ]);

            $exportClass = new MoneyTrackerExport();
            $exportClass->forUser(TgUser::user());
            $exportClass->forDates(Carbon::parse($dateFrom), Carbon::parse($dateTo));
            dispatch(new TelegramExportJob($exportClass, TgUser::get()));
            return;
        }

        \Telegram::sendMessage([
            'chat_id' => TgUser::chatId(),
            'text' => 'Please confirm your choice',
        ]);
    }
}
