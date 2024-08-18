<?php

namespace Tests\Feature\Controllers\Api\Controllers\Api\v1;

use App\Models\Icon\Icon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IconControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testGetAllIcons()
    {
        Icon::factory()->count(5)->create();
        $response = $this->getJson(route('icons.index'));

        $response->assertStatus(200)
            ->assertJsonCount(5, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'path',
                        'name',
                    ]
                ]
            ]);
    }

    public function testShowIcon()
    {
        $icon = Icon::factory()->create();
        $response = $this->getJson(route('icons.show', $icon->id));

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $icon->id,
                    'path' => $icon->path,
                    'name' => $icon->name,
                ]
            ]);
    }
}
