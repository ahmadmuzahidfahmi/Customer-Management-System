<?php

namespace Tests\Feature\Api;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerApiTest extends TestCase
{
    use RefreshDatabase;

    protected function authHeader(array $abilities = ['read', 'write']): array
    {
        $user = User::factory()->create();
        $token = $user->createToken('test', $abilities)->plainTextToken;

        return ['Authorization' => "Bearer {$token}"];
    }

    public function test_can_list_customers(): void
    {
        Customer::factory()->count(3)->create();

        $response = $this->withHeaders($this->authHeader())->getJson('/api/customers');

        $response->assertStatus(200);
        $this->assertCount(3, $response->json('data'));
    }

    public function test_can_create_customer(): void
    {
        $response = $this->withHeaders($this->authHeader())->postJson('/api/customers', [
            'Company_Name'  => 'Acme Inc',
            'Company_Email' => 'hello@acme.com',
            'Company_No'    => '0123456789',
            'Status'        => 'Active',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'name'  => 'Acme Inc',
                    'email' => 'hello@acme.com',
                ],
            ]);

        $this->assertDatabaseHas('company', ['Company_Name' => 'Acme Inc']);
    }

    public function test_creating_customer_requires_name(): void
    {
        $response = $this->withHeaders($this->authHeader())->postJson('/api/customers', [
            'Company_Email' => 'hello@acme.com',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['Company_Name']);
    }

    public function test_can_show_a_customer(): void
    {
        $customer = Customer::factory()->create();

        $response = $this->withHeaders($this->authHeader())->getJson("/api/customers/{$customer->Company_ID}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $customer->Company_ID);
    }

    public function test_showing_missing_customer_returns_404(): void
    {
        $response = $this->withHeaders($this->authHeader())->getJson('/api/customers/999999');

        $response->assertStatus(404);
    }

    public function test_can_update_a_customer(): void
    {
        $customer = Customer::factory()->create(['Company_Name' => 'Old Name']);

        $response = $this->withHeaders($this->authHeader())->putJson("/api/customers/{$customer->Company_ID}", [
            'Company_Name' => 'New Name',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'New Name');

        $this->assertDatabaseHas('company', ['Company_ID' => $customer->Company_ID, 'Company_Name' => 'New Name']);
    }

    public function test_can_delete_a_customer(): void
    {
        $customer = Customer::factory()->create();

        $response = $this->withHeaders($this->authHeader())->deleteJson("/api/customers/{$customer->Company_ID}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('company', ['Company_ID' => $customer->Company_ID]);
    }

    public function test_read_only_token_can_list_customers(): void
    {
        Customer::factory()->create();

        $response = $this->withHeaders($this->authHeader(['read']))->getJson('/api/customers');

        $response->assertStatus(200);
    }

    public function test_read_only_token_cannot_create_customer(): void
    {
        $response = $this->withHeaders($this->authHeader(['read']))->postJson('/api/customers', [
            'Company_Name'  => 'Acme Inc',
            'Company_Email' => 'hello@acme.com',
            'Status'        => 'Active',
        ]);

        $response->assertStatus(403);
    }

    public function test_read_only_token_cannot_update_customer(): void
    {
        $customer = Customer::factory()->create();

        $response = $this->withHeaders($this->authHeader(['read']))
            ->putJson("/api/customers/{$customer->Company_ID}", ['Company_Name' => 'Hacked']);

        $response->assertStatus(403);
    }

    public function test_read_only_token_cannot_delete_customer(): void
    {
        $customer = Customer::factory()->create();

        $response = $this->withHeaders($this->authHeader(['read']))
            ->deleteJson("/api/customers/{$customer->Company_ID}");

        $response->assertStatus(403);
    }

    public function test_unauthenticated_request_is_rejected(): void
    {
        $response = $this->getJson('/api/customers');

        $response->assertStatus(401);
    }
}
