<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_audit_log(): void
    {
        $admin = User::factory()->create(['User_Role' => 'Admin']);

        $response = $this->actingAs($admin)->get('/audit-log');

        $response->assertStatus(200);
    }

    public function test_non_admin_cannot_access_audit_log(): void
    {
        $staff = User::factory()->create(['User_Role' => 'Staff']);

        $response = $this->actingAs($staff)->get('/audit-log');

        $response->assertStatus(403);
    }
}