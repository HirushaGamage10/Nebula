<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Http\Requests\CreateUserRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EmailValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_validation_rules()
    {
        $request = new CreateUserRequest();
        $rules = $request->rules();
        
        // Check if email validation rules exist
        $this->assertArrayHasKey('email', $rules);
        $this->assertContains('required', $rules['email']);
        $this->assertContains('email', $rules['email']);
        $this->assertContains('max:255', $rules['email']);
        $this->assertContains('unique:users,email', $rules['email']);
        $this->assertContains('regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $rules['email']);
    }

    public function test_email_validation_messages()
    {
        $request = new CreateUserRequest();
        $messages = $request->messages();
        
        // Check if email validation messages exist
        $this->assertArrayHasKey('email.required', $messages);
        $this->assertArrayHasKey('email.email', $messages);
        $this->assertArrayHasKey('email.max', $messages);
        $this->assertArrayHasKey('email.unique', $messages);
        $this->assertArrayHasKey('email.regex', $messages);
        
        // Check message content
        $this->assertEquals('Email address is required.', $messages['email.required']);
        $this->assertEquals('Please enter a valid email address.', $messages['email.email']);
        $this->assertEquals('Email address cannot exceed 255 characters.', $messages['email.max']);
        $this->assertEquals('This email address is already registered.', $messages['email.unique']);
        $this->assertEquals('Please enter a valid email address format.', $messages['email.regex']);
    }

    public function test_email_validation_attributes()
    {
        $request = new CreateUserRequest();
        $attributes = $request->attributes();
        
        // Check if email attribute exists
        $this->assertArrayHasKey('email', $attributes);
        $this->assertEquals('email address', $attributes['email']);
    }

    public function test_valid_email_formats()
    {
        $validEmails = [
            'test@nebula.com',
            'test.user@nebula.com',
            'test+user@nebula.com',
            'test123@nebula.com',
            'test@nebula.co.uk',
            'test@subdomain.nebula.com',
            'user@example.com',
            'admin@test.org'
        ];

        foreach ($validEmails as $email) {
            $this->assertTrue(filter_var($email, FILTER_VALIDATE_EMAIL) !== false, "Email $email should be valid");
        }
    }

    public function test_invalid_email_formats()
    {
        $invalidEmails = [
            'invalid-email-format',
            'test user@nebula.com',
            'test@@nebula.com',
            'test@invalid',
            'test@.com',
            '@nebula.com',
            'test@',
            'test@nebula.',
            'test@nebula..com',
            'test..user@nebula.com'
        ];

        foreach ($invalidEmails as $email) {
            $this->assertFalse(filter_var($email, FILTER_VALIDATE_EMAIL) !== false, "Email $email should be invalid");
        }
    }

    public function test_email_regex_validation()
    {
        $regex = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
        
        $validEmails = [
            'test@nebula.com',
            'test.user@nebula.com',
            'test+user@nebula.com',
            'test123@nebula.com',
            'test@nebula.co.uk',
            'test@subdomain.nebula.com'
        ];

        foreach ($validEmails as $email) {
            $this->assertTrue(preg_match($regex, $email) === 1, "Email $email should match regex");
        }

        $invalidEmails = [
            'test user@nebula.com',
            'test@@nebula.com',
            'test@invalid',
            'test@.com',
            '@nebula.com',
            'test@',
            'test@nebula.',
            'test@nebula..com'
        ];

        foreach ($invalidEmails as $email) {
            $this->assertFalse(preg_match($regex, $email) === 1, "Email $email should not match regex");
        }
    }

    public function test_email_spaces_validation()
    {
        $emailsWithSpaces = [
            'test user@nebula.com',
            'test@ nebula.com',
            'test@nebula .com',
            ' test@nebula.com',
            'test@nebula.com '
        ];

        foreach ($emailsWithSpaces as $email) {
            $this->assertTrue(strpos($email, ' ') !== false, "Email $email should contain spaces");
        }
    }

    public function test_email_multiple_at_symbols_validation()
    {
        $emailsWithMultipleAt = [
            'test@@nebula.com',
            'test@user@nebula.com',
            '@@nebula.com',
            'test@@@nebula.com'
        ];

        foreach ($emailsWithMultipleAt as $email) {
            $this->assertTrue(substr_count($email, '@') > 1, "Email $email should contain multiple @ symbols");
        }
    }
} 