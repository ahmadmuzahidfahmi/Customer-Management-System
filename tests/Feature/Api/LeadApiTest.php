<?php

namespace Tests\Feature\Api;

use App\Models\Customer;
use App\Models\Leads;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadApiTest extends TestCase
{
    use RefreshDatabase;

    protected function authHeader(array $abilities = ['read', 'write']): array
    {
        $user = User::factory()->create();
        $token = $user->createToken('test', $abilities)->plainTextToken;

        return ['Authorization' => "Bearer {$token}"];
    }

    public function test_can_list_leads(): void
    {
        Leads::factory()->count(2)->create();

        $response = $this->withHeaders($this->authHeader())->getJson('/api/leads');

        $response->assertStatus(200);
        $this->assertCount(2, $response->json('data'));
    }

    public function test_can_create_lead(): void
    {
        $company = Customer::factory()->create();

        $response = $this->withHeaders($this->authHeader())->postJson('/api/leads', [
            'Lead_Name'  => 'Website redesign',
            'Company_ID' => $company->Company_ID,
            'Status'     => 'New',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'Website redesign')
            ->assertJsonPath('data.status', 'New');

        $this->assertDatabaseHas('leads', ['Lead_Name' => 'Website redesign']);
    }

    public function test_creating_lead_requires_valid_status(): void
    {
        $company = Customer::factory()->create();

        $response = $this->withHeaders($this->authHeader())->postJson('/api/leads', [
            'Lead_Name'  => 'Website redesign',
            'Company_ID' => $company->Company_ID,
            'Status'     => 'NotARealStatus',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['Status']);
    }

    public function test_can_show_update_and_delete_a_lead(): void
    {
        $lead = Leads::factory()->create();
        $headers = $this->authHeader();

        $this->withHeaders($headers)->getJson("/api/leads/{$lead->Lead_ID}")
            ->assertStatus(200)
            ->assertJsonPath('data.id', $lead->Lead_ID);

        $this->withHeaders($headers)->putJson("/api/leads/{$lead->Lead_ID}", [
            'Status' => 'Won',
        ])->assertStatus(200)->assertJsonPath('data.status', 'Won');

        $this->withHeaders($headers)->deleteJson("/api/leads/{$lead->Lead_ID}")
            ->assertStatus(200);

        $this->assertSoftDeleted('leads', ['Lead_ID' => $lead->Lead_ID]);
    }

    public function test_read_only_token_cannot_update_lead(): void
    {
        $lead = Leads::factory()->create();

        $response = $this->withHeaders($this->authHeader(['read']))
            ->putJson("/api/leads/{$lead->Lead_ID}", ['Status' => 'Won']);

        $response->assertStatus(403);
    }

    public function test_can_filter_leads_by_company_id(): void
    {
        $companyA = Customer::factory()->create();
        $companyB = Customer::factory()->create();

        Leads::factory()->count(2)->create(['Company_ID' => $companyA->Company_ID]);
        Leads::factory()->count(3)->create(['Company_ID' => $companyB->Company_ID]);

        $response = $this->withHeaders($this->authHeader())
            ->getJson("/api/leads?company_id={$companyA->Company_ID}");

        $response->assertStatus(200);
        $this->assertCount(2, $response->json('data'));

        foreach ($response->json('data') as $lead) {
            $this->assertSame($companyA->Company_ID, $lead['company_id']);
        }
    }
}