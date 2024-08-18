<?php

namespace Tests\Feature\Controllers\Api\Controllers\Api\v1;

use App\Models\Language\Language;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LanguageControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testGetAllLanguages()
    {
        Language::factory()->count(5)->create();

        $response = $this->getJson(route('languages.index'));

        $response->assertStatus(200)
            ->assertJsonCount(5, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'code',
                        'name',
                    ]
                ]
            ]);
    }

    public function testGetSingleLanguage()
    {
        $language = Language::factory()->create();

        $response = $this->getJson(route('languages.show', $language));

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $language->id,
                    'code' => $language->code,
                    'name' => $language->name,
                ]
            ]);
    }


}
