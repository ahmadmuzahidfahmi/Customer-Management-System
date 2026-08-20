<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login_when_visiting_customers(): void
    {
        $response = $this->get('/customers');

        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_customer_listing(): void
    {
        $user = User::factory()->create();
        Customer::factory()->count(3)->create();

        $response = $this->actingAs($user)->get('/customers');

        $response->assertStatus(200);
        $response->assertViewIs('customers');
    }

    public function test_authenticated_user_can_create_a_customer(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/customers', [
            'Company_Name'  => 'Acme Inc',
            'Company_Email' => 'contact@acme.test',
            'Country_Code'  => '+60',
            'Company_No'    => '0123456789',
            'Status'        => 'Active',
        ]);

        $response->assertRedirect(route('customers'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('company', [
            'Company_Name' => 'Acme Inc',
            'Company_Email' => 'contact@acme.test',
        ]);
    }

    public function test_creating_a_customer_requires_a_company_name(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/customers', [
            'Company_Name'  => '',
            'Country_Code'  => '+60',
            'Company_No'    => '0123456789',
            'Status'        => 'Active',
        ]);

        $response->assertSessionHasErrors('Company_Name');
        $this->assertDatabaseCount('company', 0);
    }

    public function test_authenticated_user_can_update_a_customer(): void
    {
        $user = User::factory()->create();
        $customer = Customer::factory()->create(['Company_Name' => 'Old Name']);

        $response = $this->actingAs($user)->put("/customers/{$customer->Company_ID}", [
            'Company_Name'  => 'New Name',
            'Company_Email' => $customer->Company_Email,
            'Country_Code'  => '+60',
            'Company_No'    => $customer->Company_No,
            'Status'        => $customer->Status ?? 'Active',
        ]);

        $response->assertRedirect(route('customers.show', $customer->Company_ID));
        $this->assertDatabaseHas('company', [
            'Company_ID'   => $customer->Company_ID,
            'Company_Name' => 'New Name',
        ]);
    }

    public function test_authenticated_user_can_delete_a_customer(): void
    {
        $user = User::factory()->create();
        $customer = Customer::factory()->create();

        $response = $this->actingAs($user)->delete("/customers/{$customer->Company_ID}");

        $response->assertRedirect(route('customers'));
        $this->assertSoftDeleted('company', ['Company_ID' => $customer->Company_ID]);
    }

    public function test_guest_role_account_cannot_create_a_customer(): void
    {
        $guest = User::factory()->create(['User_Role' => 'Guest']);

        $response = $this->actingAs($guest)->post('/customers', [
            'Company_Name'  => 'Blocked Co',
            'Country_Code'  => '+60',
            'Company_No'    => '0123456789',
            'Status'        => 'Active',
        ]);

        $response->assertSessionHas('guest_blocked');
        $this->assertDatabaseMissing('company', ['Company_Name' => 'Blocked Co']);
    }
}