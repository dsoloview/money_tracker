<?php

namespace App\Jobs\Import;

use App\Imports\IImport;
use App\Models\Telegram\TelegramUser;
use App\Notifications\Telegram\TelegramImportNotification;
use App\Services\Telegram\TelegramUserStateService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TelegramImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private IImport $import;
    private string $filePath;
    private TelegramUser $telegramUser;

    public function __construct(IImport $import, string $filePath, TelegramUser $telegramUser)
    {
        $this->import = $import;
        $this->filePath = $filePath;
        $this->telegramUser = $telegramUser;
    }

    public function handle(TelegramUserStateService $telegramUserStateService): void
    {
        try {
            \Excel::import($this->import, $this->filePath);

            $this->telegramUser->notify(new TelegramImportNotification(true));

            $telegramUserStateService->resetState($this->telegramUser);

            unlink($this->filePath);
        } catch (\Throwable $e) {
            report($e);
            \Log::error($e);
            $this->telegramUser->notify(new TelegramImportNotification(false));
        }
    }
}
