<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Leads;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login_when_visiting_leads(): void
    {
        $response = $this->get('/leads');

        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_lead_listing(): void
    {
        $user = User::factory()->create();
        Leads::factory()->count(3)->create();

        $response = $this->actingAs($user)->get('/leads');

        $response->assertStatus(200);
        $response->assertViewIs('leads');
    }

    public function test_authenticated_user_can_create_a_lead(): void
    {
        $user = User::factory()->create();
        $customer = Customer::factory()->create();

        $response = $this->actingAs($user)->post('/leads', [
            'Lead_Name'       => 'New Website Inquiry',
            'Source'          => 'Website',
            'Status'          => 'New',
            'Estimated_Value' => 1500,
            'Company_ID'      => $customer->Company_ID,
        ]);

        $response->assertRedirect(route('leads'));
        $this->assertDatabaseHas('leads', [
            'Lead_Name'  => 'New Website Inquiry',
            'Company_ID' => $customer->Company_ID,
        ]);
    }

    public function test_creating_a_lead_requires_a_lead_name(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/leads', [
            'Lead_Name' => '',
        ]);

        $response->assertSessionHasErrors('Lead_Name');
        $this->assertDatabaseCount('leads', 0);
    }

    public function test_kanban_board_can_be_viewed(): void
    {
        $user = User::factory()->create();
        Leads::factory()->count(2)->create(['Status' => 'New']);

        $response = $this->actingAs($user)->get('/leads/kanban');

        $response->assertStatus(200);
        $response->assertViewIs('kanban');
    }

    public function test_dragging_a_lead_updates_its_status_and_position(): void
    {
        $user = User::factory()->create();
        $lead = Leads::factory()->create(['Status' => 'New']);

        $response = $this->actingAs($user)->postJson('/leads/kanban/update-position', [
            'Lead_ID'  => $lead->Lead_ID,
            'Status'   => 'Contacted',
            'Position' => 1,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertDatabaseHas('leads', [
            'Lead_ID' => $lead->Lead_ID,
            'Status'  => 'Contacted',
        ]);
    }
}