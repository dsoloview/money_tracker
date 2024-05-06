<?php

namespace App\Imports;

use App\Imports\DTO\MoneyTrackerRow;
use App\Imports\Mappers\MoneyTrackerRowToImportRowMapper;
use App\Models\User;
use App\Services\Import\ImportService;
use App\Telegram\Enum\Import\ImportMode;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MoneyTrackerImport implements ToCollection, WithHeadingRow, IImport
{
    private ImportService $importService;

    public function __construct(
        private readonly ImportMode $importMode,
        private readonly User $user,
    ) {
        $this->importService = app(ImportService::class);
    }

    /**
     * @param  Collection  $collection
     */
    public function collection(Collection $collection): void
    {
        \DB::beginTransaction();
        try {
            foreach ($collection as $row) {
                $zenMoneyRow = MoneyTrackerRow::fromArray($row->toArray());
                $importRow = MoneyTrackerRowToImportRowMapper::map($zenMoneyRow);
                $this->importService->importRow($importRow, $this->importMode, $this->user);
            }
            \Cache::tags(["import_{$this->user->id}"])->flush();
            \DB::commit();
        } catch (\Throwable $e) {
            \DB::rollBack();
            throw $e;
        }
    }

    public function headingRow(): int
    {
        return 1;
    }
}
