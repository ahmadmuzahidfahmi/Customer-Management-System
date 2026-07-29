<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_with_correct_credentials(): void
    {
        $user = User::factory()->create([
            'User_Name'     => 'janedoe',
            'User_Password' => Hash::make('correct-password'),
        ]);

        $response = $this->postJson('/api/login', [
            'User_Name' => 'janedoe',
            'password'  => 'correct-password',
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure(['token', 'abilities', 'user' => ['id', 'name', 'role']]);

        $this->assertEquals(['read', 'write'], $response->json('abilities'));
    }

    public function test_login_fails_with_wrong_password(): void
    {
        User::factory()->create([
            'User_Name'     => 'janedoe',
            'User_Password' => Hash::make('correct-password'),
        ]);

        $response = $this->postJson('/api/login', [
            'User_Name' => 'janedoe',
            'password'  => 'wrong-password',
        ]);

        $response->assertStatus(401)
            ->assertJson(['success' => false]);
    }

    public function test_login_fails_for_unknown_user(): void
    {
        $response = $this->postJson('/api/login', [
            'User_Name' => 'nobody',
            'password'  => 'whatever',
        ]);

        $response->assertStatus(401);
    }

    public function test_read_only_login_issues_read_only_token(): void
    {
        User::factory()->create([
            'User_Name'     => 'janedoe',
            'User_Password' => Hash::make('correct-password'),
        ]);

        $response = $this->postJson('/api/login', [
            'User_Name' => 'janedoe',
            'password'  => 'correct-password',
            'read_only' => true,
        ]);

        $response->assertStatus(200);
        $this->assertEquals(['read'], $response->json('abilities'));
    }

    public function test_authenticated_user_can_fetch_own_profile(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/user');

        $response->assertStatus(200)
            ->assertJson(['User_ID' => $user->User_ID]);
    }

    public function test_unauthenticated_request_to_protected_route_is_rejected(): void
    {
        $response = $this->getJson('/api/user');

        $response->assertStatus(401);
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token', ['read', 'write'])->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/logout');

        $response->assertStatus(200);

        // The token should now be revoked
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }
}
