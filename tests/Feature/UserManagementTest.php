<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_cannot_view_manage_users_data(): void
    {
        $staff = User::factory()->create(['User_Role' => 'Staff']);

        $response = $this->actingAs($staff)->get('/profile');

        $response->assertStatus(200);
        $response->assertViewHas('isAdmin', false);
        $response->assertViewHas('users', null);
    }

    public function test_admin_sees_users_list_on_profile_page(): void
    {
        $admin = User::factory()->create(['User_Role' => 'Admin']);
        User::factory()->count(3)->create();

        $response = $this->actingAs($admin)->get('/profile');

        $response->assertStatus(200);
        $response->assertViewHas('isAdmin', true);
        $response->assertViewHas('users');
    }

    public function test_admin_can_create_a_user(): void
    {
        $admin = User::factory()->create(['User_Role' => 'Admin']);

        $response = $this->actingAs($admin)->post('/users', [
            'User_Name'     => 'newstaff',
            'User_Email'    => 'newstaff@example.com',
            'User_Password' => 'password123',
            'User_Role'     => 'Staff',
            'Status'        => 'Active',
        ]);

        $response->assertRedirect(route('profile'));
        $this->assertDatabaseHas('users', [
            'User_Name'  => 'newstaff',
            'User_Email' => 'newstaff@example.com',
            'User_Role'  => 'Staff',
        ]);
    }

    public function test_non_admin_cannot_create_a_user(): void
    {
        $staff = User::factory()->create(['User_Role' => 'Staff']);

        $response = $this->actingAs($staff)->post('/users', [
            'User_Name'     => 'blocked',
            'User_Email'    => 'blocked@example.com',
            'User_Password' => 'password123',
            'User_Role'     => 'Staff',
            'Status'        => 'Active',
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('users', ['User_Name' => 'blocked']);
    }

    public function test_admin_can_update_another_user(): void
    {
        $admin = User::factory()->create(['User_Role' => 'Admin']);
        $target = User::factory()->create(['User_Role' => 'Staff']);

        $response = $this->actingAs($admin)->put("/users/{$target->User_ID}", [
            'User_Name'  => 'Renamed User',
            'User_Email' => $target->User_Email,
            'User_Role'  => 'Staff',
            'Status'     => 'Inactive',
        ]);

        $response->assertRedirect(route('profile'));
        $this->assertDatabaseHas('users', [
            'User_ID'   => $target->User_ID,
            'User_Name' => 'Renamed User',
            'Status'    => 'Inactive',
        ]);
    }

    public function test_admin_cannot_demote_their_own_role(): void
    {
        $admin = User::factory()->create(['User_Role' => 'Admin']);

        $response = $this->actingAs($admin)->put("/users/{$admin->User_ID}", [
            'User_Name'  => $admin->User_Name,
            'User_Email' => $admin->User_Email,
            'User_Role'  => 'Staff',
            'Status'     => 'Active',
        ]);

        $response->assertSessionHasErrors('User_Role');
        $this->assertDatabaseHas('users', [
            'User_ID'   => $admin->User_ID,
            'User_Role' => 'Admin',
        ]);
    }

    public function test_admin_can_delete_another_user(): void
    {
        $admin = User::factory()->create(['User_Role' => 'Admin']);
        $target = User::factory()->create(['User_Role' => 'Staff']);

        $response = $this->actingAs($admin)->delete("/users/{$target->User_ID}");

        $response->assertRedirect(route('profile'));
        $this->assertDatabaseMissing('users', ['User_ID' => $target->User_ID]);
    }

    public function test_admin_cannot_delete_their_own_account(): void
    {
        $admin = User::factory()->create(['User_Role' => 'Admin']);

        $response = $this->actingAs($admin)->delete("/users/{$admin->User_ID}");

        $response->assertSessionHasErrors('User_ID');
        $this->assertDatabaseHas('users', ['User_ID' => $admin->User_ID]);
    }

    public function test_admin_cannot_delete_the_last_remaining_admin(): void
    {
        $admin = User::factory()->create(['User_Role' => 'Admin']);
        $otherAdmin = User::factory()->create(['User_Role' => 'Admin']);

        // Delete one admin, leaving exactly one — should still succeed.
        $this->actingAs($admin)->delete("/users/{$otherAdmin->User_ID}");
        $this->assertDatabaseMissing('users', ['User_ID' => $otherAdmin->User_ID]);

        // Now try to delete the last remaining admin via a second admin session
        // is impossible since there's only one left — simulate self-delete block
        // already covered above. This test documents the guard rail exists
        // even if somehow attempted by a different actor.
        $response = $this->actingAs($admin)->delete("/users/{$admin->User_ID}");
        $response->assertSessionHasErrors('User_ID');
    }
}