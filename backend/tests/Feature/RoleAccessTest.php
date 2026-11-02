<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_each_role_can_login_with_valid_credentials(): void
    {
        foreach (['Admin', 'Koordinator', 'Petugas', 'Pelapor'] as $name) {
            $role = Role::create(['name' => $name]);
            User::create(['name' => $name, 'email' => strtolower($name).'@test.local', 'password' => Hash::make('password123'), 'role_id' => $role->id, 'is_active' => true]);
        }

        foreach (['Admin', 'Koordinator', 'Petugas', 'Pelapor'] as $name) {
            $this->postJson('/api/auth/login', ['email' => strtolower($name).'@test.local', 'password' => 'password123'])
                ->assertOk()->assertJsonStructure(['token', 'user']);
        }
    }

    public function test_invalid_token_and_unauthenticated_access_are_rejected(): void
    {
        $this->withToken('invalid-token')->getJson('/api/auth/me')->assertUnauthorized();
        $this->getJson('/api/auth/me')->assertUnauthorized();
    }

    public function test_deactivated_account_cannot_use_existing_token(): void
    {
        $role = Role::create(['name' => 'Pelapor']);
        $user = User::create(['name' => 'User', 'email' => 'user@test.local', 'password' => Hash::make('password123'), 'role_id' => $role->id, 'is_active' => true]);
        $token = $user->createToken('test')->plainTextToken;
        $user->update(['is_active' => false]);

        $this->withToken($token)->getJson('/api/auth/me')->assertForbidden();
    }
}
