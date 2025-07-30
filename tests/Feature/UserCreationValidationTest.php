<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class UserCreationValidationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a Program Administrator (level 01) user for testing
        $this->adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin@nebula.com',
            'password' => Hash::make('password123'),
            'user_role' => 'Program Administrator (level 01)',
            'status' => '1',
            'user_location' => 'Nebula Institute of Technology – Welisara'
        ]);
    }

    public function test_user_creation_with_valid_data()
    {
        $this->actingAs($this->adminUser);

        $response = $this->post('/user/create', [
            'name' => 'Test User',
            'email' => 'test@nebula.com',
            'employee_id' => 'EMP001',
            'user_role' => 'Librarian',
            'user_location' => 'Nebula Institute of Technology – Welisara',
            'password' => 'password123'
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        
        $this->assertDatabaseHas('users', [
            'email' => 'test@nebula.com',
            'name' => 'Test User',
            'user_role' => 'Librarian'
        ]);
    }

    public function test_user_creation_with_invalid_email_format()
    {
        $this->actingAs($this->adminUser);

        $response = $this->post('/user/create', [
            'name' => 'Test User',
            'email' => 'invalid-email-format',
            'employee_id' => 'EMP001',
            'user_role' => 'Librarian',
            'user_location' => 'Nebula Institute of Technology – Welisara',
            'password' => 'password123'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    public function test_user_creation_with_empty_email()
    {
        $this->actingAs($this->adminUser);

        $response = $this->post('/user/create', [
            'name' => 'Test User',
            'email' => '',
            'employee_id' => 'EMP001',
            'user_role' => 'Librarian',
            'user_location' => 'Nebula Institute of Technology – Welisara',
            'password' => 'password123'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    public function test_user_creation_with_duplicate_email()
    {
        $this->actingAs($this->adminUser);

        // Create first user
        User::create([
            'name' => 'Existing User',
            'email' => 'existing@nebula.com',
            'password' => Hash::make('password123'),
            'user_role' => 'Librarian',
            'status' => '1',
            'user_location' => 'Nebula Institute of Technology – Welisara'
        ]);

        // Try to create second user with same email
        $response = $this->post('/user/create', [
            'name' => 'Test User',
            'email' => 'existing@nebula.com',
            'employee_id' => 'EMP002',
            'user_role' => 'Librarian',
            'user_location' => 'Nebula Institute of Technology – Welisara',
            'password' => 'password123'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    public function test_user_creation_with_email_containing_spaces()
    {
        $this->actingAs($this->adminUser);

        $response = $this->post('/user/create', [
            'name' => 'Test User',
            'email' => 'test user@nebula.com',
            'employee_id' => 'EMP001',
            'user_role' => 'Librarian',
            'user_location' => 'Nebula Institute of Technology – Welisara',
            'password' => 'password123'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    public function test_user_creation_with_multiple_at_symbols()
    {
        $this->actingAs($this->adminUser);

        $response = $this->post('/user/create', [
            'name' => 'Test User',
            'email' => 'test@@nebula.com',
            'employee_id' => 'EMP001',
            'user_role' => 'Librarian',
            'user_location' => 'Nebula Institute of Technology – Welisara',
            'password' => 'password123'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    public function test_user_creation_with_invalid_domain()
    {
        $this->actingAs($this->adminUser);

        $response = $this->post('/user/create', [
            'name' => 'Test User',
            'email' => 'test@invalid',
            'employee_id' => 'EMP001',
            'user_role' => 'Librarian',
            'user_location' => 'Nebula Institute of Technology – Welisara',
            'password' => 'password123'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    public function test_user_creation_with_valid_email_formats()
    {
        $this->actingAs($this->adminUser);

        $validEmails = [
            'test@nebula.com',
            'test.user@nebula.com',
            'test+user@nebula.com',
            'test123@nebula.com',
            'test@nebula.co.uk',
            'test@subdomain.nebula.com'
        ];

        foreach ($validEmails as $index => $email) {
            $response = $this->post('/user/create', [
                'name' => 'Test User ' . $index,
                'email' => $email,
                'employee_id' => 'EMP' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                'user_role' => 'Librarian',
                'user_location' => 'Nebula Institute of Technology – Welisara',
                'password' => 'password123'
            ]);

            $response->assertStatus(200);
            $response->assertJson(['success' => true]);
        }
    }

    public function test_user_creation_with_invalid_name()
    {
        $this->actingAs($this->adminUser);

        $response = $this->post('/user/create', [
            'name' => 'Test User 123',
            'email' => 'test@nebula.com',
            'employee_id' => 'EMP001',
            'user_role' => 'Librarian',
            'user_location' => 'Nebula Institute of Technology – Welisara',
            'password' => 'password123'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name']);
    }

    public function test_user_creation_with_weak_password()
    {
        $this->actingAs($this->adminUser);

        $response = $this->post('/user/create', [
            'name' => 'Test User',
            'email' => 'test@nebula.com',
            'employee_id' => 'EMP001',
            'user_role' => 'Librarian',
            'user_location' => 'Nebula Institute of Technology – Welisara',
            'password' => '123'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['password']);
    }

    public function test_user_creation_with_duplicate_employee_id()
    {
        $this->actingAs($this->adminUser);

        // Create first user
        User::create([
            'name' => 'Existing User',
            'email' => 'existing@nebula.com',
            'employee_id' => 'EMP001',
            'password' => Hash::make('password123'),
            'user_role' => 'Librarian',
            'status' => '1',
            'user_location' => 'Nebula Institute of Technology – Welisara'
        ]);

        // Try to create second user with same employee ID
        $response = $this->post('/user/create', [
            'name' => 'Test User',
            'email' => 'test@nebula.com',
            'employee_id' => 'EMP001',
            'user_role' => 'Librarian',
            'user_location' => 'Nebula Institute of Technology – Welisara',
            'password' => 'password123'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['employee_id']);
    }

    public function test_user_creation_with_invalid_role()
    {
        $this->actingAs($this->adminUser);

        $response = $this->post('/user/create', [
            'name' => 'Test User',
            'email' => 'test@nebula.com',
            'employee_id' => 'EMP001',
            'user_role' => 'Invalid Role',
            'user_location' => 'Nebula Institute of Technology – Welisara',
            'password' => 'password123'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['user_role']);
    }

    public function test_user_creation_with_invalid_location()
    {
        $this->actingAs($this->adminUser);

        $response = $this->post('/user/create', [
            'name' => 'Test User',
            'email' => 'test@nebula.com',
            'employee_id' => 'EMP001',
            'user_role' => 'Librarian',
            'user_location' => 'Invalid Location',
            'password' => 'password123'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['user_location']);
    }

    public function test_non_admin_cannot_create_user()
    {
        // Create a regular user
        $regularUser = User::create([
            'name' => 'Regular User',
            'email' => 'regular@nebula.com',
            'password' => Hash::make('password123'),
            'user_role' => 'Librarian',
            'status' => '1',
            'user_location' => 'Nebula Institute of Technology – Welisara'
        ]);

        $this->actingAs($regularUser);

        $response = $this->post('/user/create', [
            'name' => 'Test User',
            'email' => 'test@nebula.com',
            'employee_id' => 'EMP001',
            'user_role' => 'Librarian',
            'user_location' => 'Nebula Institute of Technology – Welisara',
            'password' => 'password123'
        ]);

        $response->assertStatus(403);
    }
} 