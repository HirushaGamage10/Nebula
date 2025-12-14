<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_the_application_redirects_to_login()
    {
        $response = $this->get('/');

        $loginResponse = $this->get('/login');

        $loginResponse->assertStatus(200);
    }
}
