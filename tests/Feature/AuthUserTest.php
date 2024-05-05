<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthUserTest extends TestCase
{

    use RefreshDatabase;
    use WithFaker;

    public function test_a_user_can_register_and_login()
    {
        // $this->withoutExceptionHandling();
        //TODO test attempts to login
        //TODO test session lifetime
        $password = Hash::make('password');
        $email = $this->faker->email;

        $this->json('POST', '/api/register', [
            'name' => $this->faker->name,
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $password,
        ])->assertCreated();

        $this->assertDatabaseHas('users',['email' => $email]);

        $response = $this->json('POST', '/api/login', [
            'email' => $email,
            'password' => $password
        ])
        ->assertOk();
        $this->assertAuthenticated();
        $this->assertArrayHasKey('token',$response['data']);
    }

    public function test_if_user_email_is_not_available_then_it_return_error()
    {
        $this->json('POST', '/api/login', [
            'email' => $this->faker->email,
            'password' => Hash::make('password')
        ])
        ->assertUnauthorized();
    }

    public function test_it_raise_error_if_password_is_incorrect()
    {
        $user = User::factory()->create();

        $this->json('POST', '/api/login', [
            'email' => $user->email,
            'password' => Hash::make('password')
        ])
        ->assertUnauthorized();
    }
}
