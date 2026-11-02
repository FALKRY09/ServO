<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiContractTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_validation_returns_standard_json(): void
    {
        $this->postJson('/api/auth/login', [])->assertStatus(422)
            ->assertJsonStructure(['message', 'errors']);
    }

    public function test_missing_api_route_returns_json(): void
    {
        $this->getJson('/api/does-not-exist')->assertNotFound()
            ->assertJsonStructure(['message']);
    }
}
