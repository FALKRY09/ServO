<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_user_can_login_and_access_profile(): void
    {
        $role = Role::create(['name' => 'Admin']);
        $user = User::create([
            'name' => 'Admin',
            'email' => 'admin@test.local',
            'password' => Hash::make('password123'),
            'role_id' => $role->id,
            'is_active' => true,
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertOk()->assertJsonStructure(['token', 'user']);
        $this->withToken($response->json('token'))->getJson('/api/auth/me')->assertOk();
    }

    public function test_inactive_user_cannot_login(): void
    {
        $role = Role::create(['name' => 'Pelapor']);
        User::create([
            'name' => 'Inactive',
            'email' => 'inactive@test.local',
            'password' => Hash::make('password123'),
            'role_id' => $role->id,
            'is_active' => false,
        ]);

        $this->postJson('/api/auth/login', [
            'email' => 'inactive@test.local',
            'password' => 'password123',
        ])->assertUnauthorized();
    }
}
