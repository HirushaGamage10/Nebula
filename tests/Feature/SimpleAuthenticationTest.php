<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class SimpleAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_with_empty_fields_shows_validation_errors()
    {
        $response = $this->post('/login', [
            'email' => '',
            'password' => ''
        ]);

        $response->assertSessionHasErrors(['email', 'password']);
        $this->assertGuest();
    }

    public function test_login_with_invalid_email_format()
    {
        $response = $this->post('/login', [
            'email' => 'invalid-email-format',
            'password' => 'password123'
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    public function test_login_with_valid_email_format()
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password123'
        ]);

        // Should not have email validation errors
        $response->assertSessionMissing(['email']);
        $this->assertGuest();
    }

    public function test_login_form_preserves_old_input()
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword'
        ]);

        $response->assertSessionHas('_old_input');
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

    public function test_login_with_valid_credentials_but_inactive_user()
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

    public function test_login_with_valid_credentials_but_no_role()
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

    public function test_login_with_valid_credentials_and_active_user()
    {
        // Create an active user
        $user = User::create([
            'name' => 'Active User',
            'email' => 'active@nebula.com',
            'password' => Hash::make('password123'),
            'user_role' => 'Librarian',
            'status' => '1', // Active
            'user_location' => 'Nebula Institute of Technology – Welisara'
        ]);

        $response = $this->post('/login', [
            'email' => 'active@nebula.com',
            'password' => 'password123'
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
    }

    public function test_login_with_wrong_password()
    {
        // Create an active user
        $user = User::create([
            'name' => 'Active User',
            'email' => 'active@nebula.com',
            'password' => Hash::make('password123'),
            'user_role' => 'Librarian',
            'status' => '1',
            'user_location' => 'Nebula Institute of Technology – Welisara'
        ]);

        $response = $this->post('/login', [
            'email' => 'active@nebula.com',
            'password' => 'wrongpassword'
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }
} 