<?php

namespace Tests\Feature\Controllers\Api\Controllers\Api\v1;

use Tests\TestCase;

class SearchControllerTest extends TestCase
{
    public function testSearch()
    {
        $response = $this->get('/api/v1/search?query=Test');

        $response->assertStatus(200);
    }


}
