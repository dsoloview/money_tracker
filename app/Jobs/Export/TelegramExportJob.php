<?php

namespace App\Jobs\Export;

use App\Exports\IExport;
use App\Models\Telegram\TelegramUser;
use App\Notifications\Telegram\TelegramExportNotification;
use App\Services\Telegram\TelegramUserStateService;
use Excel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Telegram\Bot\FileUpload\InputFile;

class TelegramExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private IExport $export;
    private TelegramUser $telegramUser;

    public function __construct(IExport $export, TelegramUser $telegramUser)
    {
        $this->export = $export;
        $this->telegramUser = $telegramUser;
    }

    public function handle(TelegramUserStateService $telegramUserStateService): void
    {
        $filename = $this->telegramUser->telegram_id.'_'.now()->format('Y-m-d_H-i-s');
        $path = 'telegram/exports/'.$filename;

        try {
            Excel::store($this->export, $path.'.xlsx');
            $zipPath = \Storage::path($path.'.zip');

            $zip = new \ZipArchive();
            if ($zip->open($zipPath, \ZipArchive::CREATE) === true) {
                $zip->addFile(\Storage::path($path.'.xlsx'), "$filename.xlsx");
                $zip->close();
            }

            $telegramInputFile = InputFile::create($zipPath);

            $this->telegramUser->notify(new TelegramExportNotification(true, $telegramInputFile));
            
            $telegramUserStateService->resetState($this->telegramUser);
        } catch (\Throwable $e) {
            report($e);
            \Log::error($e);
            $this->telegramUser->notify(new TelegramExportNotification(false));
        } finally {
            \Storage::delete($path.'.xlsx');
            \Storage::delete($path.'.zip');
        }
    }
}
