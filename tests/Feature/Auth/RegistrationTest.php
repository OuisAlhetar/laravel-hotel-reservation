<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'username' => 'taha',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            "first_name"=> "Taha",
            "last_name"=> "Mushed",
            "phone"=> "774798468",
            "address"=> "60-street Sana'a",
            
        ]);

        $this->assertAuthenticated();
        $response->assertNoContent();
    }
}