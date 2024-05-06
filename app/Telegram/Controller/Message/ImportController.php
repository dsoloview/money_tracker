<?php

namespace App\Telegram\Controller\Message;

use App\Enums\Import\ImportFormat;
use App\Jobs\Import\TelegramImportJob;
use App\Services\Telegram\TelegramUserStateService;
use App\Telegram\Enum\Import\ImportMode;
use App\Telegram\Enum\State\Step\TelegramImportStep;
use App\Telegram\Enum\State\TelegramState;
use App\Telegram\Facades\TgUser;
use App\Telegram\Services\TelegramKeyboardService;
use Telegram\Bot\Objects\Update;

class ImportController extends AbstractMessageController
{
    public function __construct(
        private readonly TelegramUserStateService $telegramUserStateService
    ) {
    }

    protected function importMode(Update $update): void
    {
        $importMode = $update->getMessage()->getText();

        if (!ImportMode::validateImportMode($importMode)) {
            \Telegram::sendMessage([
                'chat_id' => $update->getMessage()->getChat()->getId(),
                'text' => 'Please select import mode: 1 if you want to create categories/accounts automatically, 2 if you want to import only categories/accounts you already have.',
                'reply_markup' => TelegramKeyboardService::getImportModeKeyboard()
            ]);

            return;
        }

        $this->telegramUserStateService->updateOrCreateStateByTelegramId(
            $update->getMessage()->getFrom()->getId(),
            TelegramState::IMPORT,
            [
                'step' => TelegramImportStep::IMPORT_FORMAT->value,
                'import_mode' => $importMode
            ]
        );

        \Telegram::sendMessage([
            'chat_id' => $update->getMessage()->getChat()->getId(),
            'text' => 'Please select import format: 1 if you want to import from Money Tracker, 2 if you want to import from Zen Money.',
            'reply_markup' => TelegramKeyboardService::getImportFormatKeyboard()
        ]);
    }

    protected function importFormat(Update $update): void
    {
        $importFormat = $update->getMessage()->getText();
        $importMode = TgUser::state()?->data['import_mode'];

        if (!ImportFormat::validateImportFormat($importFormat)) {
            \Telegram::sendMessage([
                'chat_id' => $update->getMessage()->getChat()->getId(),
                'text' => 'Please select import format: 1 if you want to import from Money Tracker, 2 if you want to import from Zen Money.',
                'reply_markup' => TelegramKeyboardService::getImportFormatKeyboard()
            ]);

            return;
        }

        $this->telegramUserStateService->updateOrCreateStateByTelegramId(
            $update->getMessage()->getFrom()->getId(),
            TelegramState::IMPORT,
            [
                'step' => TelegramImportStep::IMPORT_FILE->value,
                'import_format' => $importFormat,
                'import_mode' => $importMode
            ]
        );

        \Telegram::sendMessage([
            'chat_id' => $update->getMessage()->getChat()->getId(),
            'text' => 'Please send your zen money export file.',
        ]);
    }

    protected function importFile(Update $update): void
    {
        $importMode = TgUser::state()?->data['import_mode'];
        $importFormat = TgUser::state()?->data['import_format'];
        $importFormat = ImportFormat::from($importFormat);

        if ($update->getMessage()->getDocument() === null) {
            \Telegram::sendMessage([
                'chat_id' => $update->getMessage()->getChat()->getId(),
                'text' => 'Please send your zen money export file.',
            ]);

            return;
        }

        $fileId = $update->getMessage()->getDocument()->getFileId();

        $file = \Telegram::getFile(['file_id' => $fileId]);

        $size = $file->file_size;

        if ($size > 2097152) {
            \Telegram::sendMessage([
                'chat_id' => $update->getMessage()->getChat()->getId(),
                'text' => 'File is too big. Please send file less than 2MB.',
            ]);

            return;
        }

        $extension = pathinfo($file->file_path, PATHINFO_EXTENSION);

        if (!$importFormat->validateFileExtension($extension)) {
            \Telegram::sendMessage([
                'chat_id' => $update->getMessage()->getChat()->getId(),
                'text' => 'Please send your zen money export file.',
            ]);

            return;
        }

        $filePath = storage_path('app/telegram/imports/'.$fileId.'.'.$extension);
        \Telegram::downloadFile($file, $filePath);

        $importClass = $importFormat->getImportClass();

        $import = new $importClass(
            ImportMode::from($importMode),
            TgUser::user()
        );

        dispatch(new TelegramImportJob($import, $filePath, TgUser::get()));
    }
}
