<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;

class AuthenticationValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_with_valid_credentials()
    {
        // Create a test user
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@nebula.com',
            'password' => Hash::make('password123'),
            'user_role' => 'Librarian',
            'status' => '1',
            'user_location' => 'Nebula Institute of Technology – Welisara'
        ]);

        // Attempt login
        $response = $this->post('/login', [
            'email' => 'test@nebula.com',
            'password' => 'password123'
        ]);

        // Should redirect to dashboard
        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
    }

    public function test_login_with_invalid_email()
    {
        $response = $this->post('/login', [
            'email' => 'invalid-email',
            'password' => 'password123'
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    public function test_login_with_empty_fields()
    {
        $response = $this->post('/login', [
            'email' => '',
            'password' => ''
        ]);

        $response->assertSessionHasErrors(['email', 'password']);
        $this->assertGuest();
    }

    public function test_login_with_nonexistent_user()
    {
        $response = $this->post('/login', [
            'email' => 'nonexistent@nebula.com',
            'password' => 'password123'
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    public function test_login_with_wrong_password()
    {
        // Create a test user
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@nebula.com',
            'password' => Hash::make('password123'),
            'user_role' => 'Librarian',
            'status' => '1',
            'user_location' => 'Nebula Institute of Technology – Welisara'
        ]);

        $response = $this->post('/login', [
            'email' => 'test@nebula.com',
            'password' => 'wrongpassword'
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    public function test_login_with_inactive_user()
    {
        // Create an inactive user
        $user = User::create([
            'name' => 'Inactive User',
            'email' => 'inactive@nebula.com',
            'password' => Hash::make('password123'),
            'user_role' => 'Librarian',
            'status' => '0', // Inactive
            'user_location' => 'Nebula Institute of Technology – Welisara'
        ]);

        $response = $this->post('/login', [
            'email' => 'inactive@nebula.com',
            'password' => 'password123'
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    public function test_login_with_user_without_role()
    {
        // Create a user without role
        $user = User::create([
            'name' => 'No Role User',
            'email' => 'norole@nebula.com',
            'password' => Hash::make('password123'),
            'user_role' => null,
            'status' => '1',
            'user_location' => 'Nebula Institute of Technology – Welisara'
        ]);

        $response = $this->post('/login', [
            'email' => 'norole@nebula.com',
            'password' => 'password123'
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    public function test_login_throttling()
    {
        // Clear any existing cache
        Cache::flush();

        // Attempt multiple failed logins
        for ($i = 0; $i < 6; $i++) {
            $response = $this->post('/login', [
                'email' => 'test@nebula.com',
                'password' => 'wrongpassword'
            ]);
        }

        // The 6th attempt should be blocked
        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    public function test_login_form_displays_validation_errors()
    {
        $response = $this->get('/login');
        
        // Submit form with invalid data
        $response = $this->post('/login', [
            'email' => 'invalid-email',
            'password' => ''
        ]);

        // Should return to login page with errors
        $response->assertStatus(302);
        $response->assertSessionHasErrors(['email', 'password']);
    }

    public function test_successful_login_redirects_to_dashboard()
    {
        // Create a test user
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@nebula.com',
            'password' => Hash::make('password123'),
            'user_role' => 'Librarian',
            'status' => '1',
            'user_location' => 'Nebula Institute of Technology – Welisara'
        ]);

        $response = $this->post('/login', [
            'email' => 'test@nebula.com',
            'password' => 'password123'
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
    }

    public function test_login_preserves_old_input_on_failure()
    {
        $response = $this->post('/login', [
            'email' => 'test@nebula.com',
            'password' => 'wrongpassword'
        ]);

        $response->assertSessionHas('_old_input');
        $this->assertGuest();
    }
} 