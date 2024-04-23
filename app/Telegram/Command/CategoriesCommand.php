<?php

namespace App\Telegram\Command;

use App\Services\Category\CategoryService;
use App\Telegram\Facades\TgUser;
use Telegram\Bot\Commands\Command;

class CategoriesCommand extends Command
{
    protected string $name = 'categories';

    protected string $description = 'Get info about your categories';

    public function __construct(
        private readonly CategoryService $categoryService
    ) {
    }

    public function handle()
    {
        $categories = $this->categoryService->getUsersCategoriesTree(TgUser::user());
        $categories = $this->categoryService->groupCategoriesByType($categories);

        foreach ($categories as $type => $categoriesByType) {
            $this->replyWithMessage([
                'text' => \Blade::render('telegram.categories', [
                    'categories' => $categoriesByType,
                    'type' => $type,
                ]),
                'parse_mode' => 'HTML',
            ]);
        }
    }
}
