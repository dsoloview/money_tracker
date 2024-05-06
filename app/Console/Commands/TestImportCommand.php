<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Storage;
use Telegram\Bot\FileUpload\InputFile;

class TestImportCommand extends Command
{
    protected $signature = 'test:import';

    protected $description = 'Command description';

    public function handle(): void
    {
        $path = 'telegram/exports/316447157_2024-05-05_18-55-10';
        $filePath = Storage::path($path.'.xlsx');
        $zipPath = Storage::path($path.'.zip');
        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE) === true) {
            $zip->addFile($filePath, '316447157_2024-05-05_18-55-10.xlsx');
            $zip->close();
        }

        $link = Storage::disk('public')->url($path.'.xlsx');

        $telegramInputFile = InputFile::create($zipPath);
        \Telegram::sendDocument([
            'chat_id' => 316447157,
            'document' => $telegramInputFile,
        ]);

        $this->info($link);

        $this->info('here');
    }
}
