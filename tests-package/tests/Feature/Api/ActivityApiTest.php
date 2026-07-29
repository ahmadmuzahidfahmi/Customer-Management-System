<?php

namespace Tests\Feature\Api;

use App\Models\Activity;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityApiTest extends TestCase
{
    use RefreshDatabase;

    protected function authHeader(array $abilities = ['read', 'write']): array
    {
        $user = User::factory()->create();
        $token = $user->createToken('test', $abilities)->plainTextToken;

        return ['Authorization' => "Bearer {$token}"];
    }

    public function test_can_list_activities(): void
    {
        Activity::factory()->count(2)->create();

        $response = $this->withHeaders($this->authHeader())->getJson('/api/activities');

        $response->assertStatus(200);
        $this->assertCount(2, $response->json('data'));
    }

    public function test_can_filter_activities_by_status(): void
    {
        Activity::factory()->create(['Status' => 'Pending']);
        Activity::factory()->create(['Status' => 'Completed']);

        $response = $this->withHeaders($this->authHeader())->getJson('/api/activities?status=Completed');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
        $this->assertEquals('Completed', $response->json('data.0.status'));
    }

    public function test_can_create_activity_linked_to_a_contact(): void
    {
        $contact = Contact::factory()->create();

        $response = $this->withHeaders($this->authHeader())->postJson('/api/activities', [
            'Activity_Type' => 'Call',
            'Subject'       => 'Intro call',
            'Contact_ID'    => $contact->Contact_ID,
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.subject', 'Intro call')
            ->assertJsonPath('data.status', 'Pending');

        $this->assertDatabaseHas('activities', ['Subject' => 'Intro call']);
    }

    public function test_creating_activity_without_lead_or_contact_fails(): void
    {
        $response = $this->withHeaders($this->authHeader())->postJson('/api/activities', [
            'Activity_Type' => 'Call',
            'Subject'       => 'Intro call',
        ]);

        $response->assertStatus(422)
            ->assertJson(['success' => false]);
    }

    public function test_date_only_deadline_is_set_to_end_of_day(): void
    {
        $contact = Contact::factory()->create();

        $response = $this->withHeaders($this->authHeader())->postJson('/api/activities', [
            'Activity_Type' => 'Call',
            'Subject'       => 'Intro call',
            'Contact_ID'    => $contact->Contact_ID,
            'Dead_Line'     => '2026-08-01',
        ]);

        $response->assertStatus(201);

        $activity = Activity::first();
        $this->assertEquals('23:59:00', $activity->Dead_Line->format('H:i:s'));
    }

    public function test_can_update_activity_status(): void
    {
        $activity = Activity::factory()->create(['Status' => 'Pending']);

        $response = $this->withHeaders($this->authHeader())->putJson("/api/activities/{$activity->Activity_ID}", [
            'Status' => 'Completed',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.status', 'Completed');
    }

    public function test_can_delete_activity(): void
    {
        $activity = Activity::factory()->create();

        $response = $this->withHeaders($this->authHeader())->deleteJson("/api/activities/{$activity->Activity_ID}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('activities', ['Activity_ID' => $activity->Activity_ID]);
    }

    public function test_read_only_token_cannot_create_activity(): void
    {
        $contact = Contact::factory()->create();

        $response = $this->withHeaders($this->authHeader(['read']))->postJson('/api/activities', [
            'Activity_Type' => 'Call',
            'Subject'       => 'Intro call',
            'Contact_ID'    => $contact->Contact_ID,
        ]);

        $response->assertStatus(403);
    }
}
