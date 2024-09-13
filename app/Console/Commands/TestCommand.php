<?php

namespace App\Console\Commands;

use App\Enums\Category\CategoryTransactionType;
use App\Models\User;
use Illuminate\Console\Command;

class TestCommand extends Command
{
    protected $signature = 'test:test';

    protected $description = 'Command description';

    public function handle(): void
    {
        $categoryService = app(\App\Services\Category\CategoryService::class);
        $user = User::find(1);

        $categories = $categoryService->getUsersCategoriesByNamesAndType($user, ['Продукты'],
            CategoryTransactionType::EXPENSE);

        dd($categories);
    }
}
