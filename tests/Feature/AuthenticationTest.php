<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'User_Password' => Hash::make('password'),
        ]);

        $response = $this->post('/login', [
            'User_Name' => $user->User_Name,
            'password'  => 'password',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('dashboard'));
    }

    public function test_user_cannot_login_with_invalid_password(): void
    {
        $user = User::factory()->create([
            'User_Password' => Hash::make('password'),
        ]);

        $response = $this->post('/login', [
            'User_Name' => $user->User_Name,
            'password'  => 'wrong-password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('User_Name');
    }

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect(route('login'));
    }

    public function test_guest_login_creates_and_signs_in_a_guest_account(): void
    {
        $response = $this->post('/login/guest');

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'User_Name' => 'guest',
            'User_Role' => 'Guest',
        ]);
        $response->assertRedirect(route('dashboard'));
    }

    public function test_guest_login_reuses_the_same_guest_account_on_repeat_visits(): void
    {
        $this->post('/login/guest');
        $firstGuestCount = User::where('User_Name', 'guest')->count();

        auth()->logout();

        $this->post('/login/guest');
        $secondGuestCount = User::where('User_Name', 'guest')->count();

        $this->assertSame(1, $firstGuestCount);
        $this->assertSame(1, $secondGuestCount);
    }

    public function test_new_user_can_register(): void
    {
        $response = $this->post('/register', [
            'User_Name'             => 'newstaffuser',
            'User_Email'            => 'newstaff@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'User_Name'  => 'newstaffuser',
            'User_Email' => 'newstaff@example.com',
            'User_Role'  => 'Staff',
        ]);
        $response->assertRedirect(route('dashboard'));
    }

    public function test_registration_requires_matching_password_confirmation(): void
    {
        $response = $this->post('/register', [
            'User_Name'             => 'baduser',
            'User_Email'            => 'baduser@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'does-not-match',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('password');
    }
}