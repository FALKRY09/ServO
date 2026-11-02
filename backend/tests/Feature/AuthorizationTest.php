<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Priority;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_reporter_cannot_assign_or_advance_an_open_ticket(): void
    {
        $role = Role::create(['name' => 'Pelapor']);
        $user = User::factory()->create(['role_id' => $role->id]);
        $category = Category::create(['name' => 'Software']);
        $priority = Priority::create(['name' => 'Normal', 'level' => 2]);
        $ticket = $this->actingAs($user)->postJson('/api/tickets', [
            'title' => 'Issue', 'description' => 'Details', 'category_id' => $category->id, 'priority_id' => $priority->id,
        ])->json();

        $this->actingAs($user)->patchJson('/api/tickets/'.$ticket['id'].'/status', ['status' => 'In Progress'])->assertForbidden();
        $this->actingAs($user)->postJson('/api/tickets/'.$ticket['id'].'/assignments', ['assignee_id' => $user->id])->assertForbidden();
    }
}
