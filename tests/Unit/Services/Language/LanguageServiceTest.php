<?php

namespace Tests\Unit\Services\Language;

use App\Models\Language\Language;
use App\Services\Language\LanguageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class LanguageServiceTest extends TestCase
{
    use RefreshDatabase;

    private LanguageService $languageService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->languageService = app(LanguageService::class);
    }

    public function testIndexFetchesAllLanguages()
    {
        Language::factory()->count(5)->create();

        $languages = $this->languageService->index();

        $this->assertInstanceOf(Collection::class, $languages);
        $this->assertContainsOnlyInstancesOf(Language::class, $languages);
        $this->assertCount(5, $languages);
    }

    public function testIndexUsesCache()
    {
        Language::factory()->count(5)->create();
        
        Cache::shouldReceive('remember')
            ->once()
            ->with('languages', 60 * 60 * 24, \Closure::class)
            ->andReturn(collect());

        $languages = $this->languageService->index();

        $this->assertInstanceOf(Collection::class, $languages);
    }
}
