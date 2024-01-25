<?php

namespace Tests\Feature\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DeleteUserTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('passport:install');
    }

    public function testDeleteUserSuccessfully(): void
    {
        $data = [
            'name' => $this->faker->name,
            'email' => 'unittest_' . $this->faker->email,
            'password' => 'Password!@#123',
            'password_confirmation' => 'Password!@#123',
            'role' => 'admin',
        ];

        $user = $this->postJson('/api/user', $data);

        $token = $user->json('data.token');

        $this->deleteJson('/api/user/' . $user->json('data.user.id'), [], [
            'Authorization' => 'Bearer ' . $token,
        ])
        ->assertStatus(200);
    }
}
