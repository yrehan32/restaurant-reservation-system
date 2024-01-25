<?php

namespace Tests\Feature\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GetUserTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('passport:install');
    }

    public function testGetUserSuccessfully(): void
    {
        $name = $this->faker->name;
        $email = 'unittest_' . $this->faker->email;
        $password = 'Password!@#123';

        $user = $this->postJson('/api/user', [
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $password,
            'role' => 'admin',
        ]);

        $token = $user->json('data.token');

        $this->get('/api/user', [
            'Authorization' => 'Bearer ' . $token,
        ])
        ->assertStatus(200);
    }

    public function testGetUserUnauthorized(): void
    {
        $token = $this->faker->password;

        $this->get('/api/user', [
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])
        ->assertStatus(401);
    }
}
