<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\v1\Search\SearchController;
use App\Http\Requests\Search\SearchRequest;
use Illuminate\Console\Command;

class TestCommand extends Command
{
    protected $signature = 'test:test';

    protected $description = 'Command description';

    public function handle(): void
    {
        $controller = app(SearchController::class);
        $controller->search(new SearchRequest([
            'query' => 'Test'
        ]));
    }
}
