<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Department;
use App\Models\Priority;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketTest extends TestCase
{
    use RefreshDatabase;

    public function test_reporter_can_create_and_progress_own_ticket(): void
    {
        $role = Role::create(['name' => 'Pelapor']);
        $user = User::factory()->create(['role_id' => $role->id, 'is_active' => true]);
        $category = Category::create(['name' => 'Network']);
        $priority = Priority::create(['name' => 'Normal', 'level' => 2]);

        $response = $this->actingAs($user)->postJson('/api/tickets', [
            'title' => 'Network issue', 'description' => 'No connection',
            'category_id' => $category->id, 'priority_id' => $priority->id,
        ]);

        $response->assertCreated()->assertJsonPath('status', 'Open');
        $ticketId = $response->json('id');
        $this->actingAs($user)->patchJson("/api/tickets/{$ticketId}/status", ['status' => 'Resolved'])
            ->assertForbidden();
    }

    public function test_reporter_cannot_view_another_users_ticket(): void
    {
        $role = Role::create(['name' => 'Pelapor']);
        $owner = User::factory()->create(['role_id' => $role->id]);
        $other = User::factory()->create(['role_id' => $role->id]);
        $category = Category::create(['name' => 'Hardware']);
        $priority = Priority::create(['name' => 'Low', 'level' => 3]);

        $ticket = $this->actingAs($owner)->postJson('/api/tickets', [
            'title' => 'Keyboard', 'description' => 'Broken',
            'category_id' => $category->id, 'priority_id' => $priority->id,
        ])->json();

        $this->actingAs($other)->getJson('/api/tickets/'.$ticket['id'])->assertForbidden();
    }

    public function test_requester_can_edit_open_ticket_and_filter_list(): void
    {
        $role = Role::create(['name' => 'Pelapor']);
        $user = User::factory()->create(['role_id' => $role->id, 'is_active' => true]);
        $category = Category::create(['name' => 'Access']);
        $priority = Priority::create(['name' => 'High', 'level' => 1]);
        $ticket = $this->actingAs($user)->postJson('/api/tickets', ['title' => 'Old title', 'description' => 'Details', 'category_id' => $category->id, 'priority_id' => $priority->id])->json();

        $this->actingAs($user)->putJson('/api/tickets/'.$ticket['id'], ['title' => 'Updated title'])->assertOk()->assertJsonPath('title', 'Updated title');
        $this->actingAs($user)->getJson('/api/tickets?search=Updated')->assertOk()->assertJsonCount(1, 'data');
    }
}
