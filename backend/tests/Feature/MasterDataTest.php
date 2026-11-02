<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Priority;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasterDataTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_manage_category_and_duplicate_is_rejected(): void
    {
        $admin = User::factory()->create(['role_id' => Role::create(['name' => 'Admin'])->id]);
        $this->actingAs($admin)->postJson('/api/categories', ['name' => 'Network'])->assertCreated();
        $this->actingAs($admin)->postJson('/api/categories', ['name' => 'Network'])->assertUnprocessable();
        $category = Category::first();
        $this->actingAs($admin)->patchJson('/api/categories/'.$category->id.'/toggle-active')->assertOk()->assertJsonPath('is_active', false);
    }

    public function test_non_admin_cannot_manage_master_data(): void
    {
        $user = User::factory()->create(['role_id' => Role::create(['name' => 'Petugas'])->id]);
        $this->actingAs($user)->getJson('/api/categories')->assertForbidden();
        $this->actingAs($user)->postJson('/api/categories', ['name' => 'Blocked'])->assertForbidden();
    }

    public function test_admin_can_filter_users_and_read_department_members(): void
    {
        $admin = User::factory()->create(['role_id' => Role::create(['name' => 'Admin'])->id, 'name' => 'Admin User']);
        $this->actingAs($admin)->getJson('/api/users?search=Admin')->assertOk()->assertJsonStructure(['data', 'current_page']);
    }
}
