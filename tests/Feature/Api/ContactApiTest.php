<?php

namespace Tests\Feature\Api;

use App\Models\Contact;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactApiTest extends TestCase
{
    use RefreshDatabase;

    protected function authHeader(array $abilities = ['read', 'write']): array
    {
        $user = User::factory()->create();
        $token = $user->createToken('test', $abilities)->plainTextToken;

        return ['Authorization' => "Bearer {$token}"];
    }

    public function test_can_list_contacts(): void
    {
        Contact::factory()->count(2)->create();

        $response = $this->withHeaders($this->authHeader())->getJson('/api/contacts');

        $response->assertStatus(200);
        $this->assertCount(2, $response->json('data'));
    }

    public function test_can_create_contact(): void
    {
        $company = Customer::factory()->create();

        $response = $this->withHeaders($this->authHeader())->postJson('/api/contacts', [
            'Contact_Name'  => 'John Tan',
            'Contact_Email' => 'john@acme.com',
            'Company_ID'    => $company->Company_ID,
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'John Tan');

        $this->assertDatabaseHas('contacts', ['Contact_Name' => 'John Tan']);
    }

    public function test_creating_contact_requires_a_company(): void
    {
        $response = $this->withHeaders($this->authHeader())->postJson('/api/contacts', [
            'Contact_Name' => 'John Tan',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['Company_ID']);
    }

    public function test_can_show_update_and_delete_a_contact(): void
    {
        $contact = Contact::factory()->create();
        $headers = $this->authHeader();

        $this->withHeaders($headers)->getJson("/api/contacts/{$contact->Contact_ID}")
            ->assertStatus(200)
            ->assertJsonPath('data.id', $contact->Contact_ID);

        $this->withHeaders($headers)->putJson("/api/contacts/{$contact->Contact_ID}", [
            'Contact_Name' => 'Updated Name',
        ])->assertStatus(200)->assertJsonPath('data.name', 'Updated Name');

        $this->withHeaders($headers)->deleteJson("/api/contacts/{$contact->Contact_ID}")
            ->assertStatus(200);

        $this->assertSoftDeleted('contacts', ['Contact_ID' => $contact->Contact_ID]);
    }

    public function test_read_only_token_cannot_create_contact(): void
    {
        $company = Customer::factory()->create();

        $response = $this->withHeaders($this->authHeader(['read']))->postJson('/api/contacts', [
            'Contact_Name' => 'John Tan',
            'Company_ID'   => $company->Company_ID,
        ]);

        $response->assertStatus(403);
    }

    public function test_can_filter_contacts_by_company_id(): void
    {
        $companyA = Customer::factory()->create();
        $companyB = Customer::factory()->create();

        Contact::factory()->count(2)->create(['Company_ID' => $companyA->Company_ID]);
        Contact::factory()->count(3)->create(['Company_ID' => $companyB->Company_ID]);

        $response = $this->withHeaders($this->authHeader())
            ->getJson("/api/contacts?company_id={$companyA->Company_ID}");

        $response->assertStatus(200);
        $this->assertCount(2, $response->json('data'));

        foreach ($response->json('data') as $contact) {
            $this->assertSame($companyA->Company_ID, $contact['company_id']);
        }
    }
}