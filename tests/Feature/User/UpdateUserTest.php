<?php

namespace Tests\Feature\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UpdateUserTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('passport:install');
    }

    public function testUpdateUserErrorValidation(): void
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

        $this->putJson('/api/user/' . $user->json('data.user.id'), [], [
            'Authorization' => 'Bearer ' . $token,
        ])
        ->assertStatus(422);
    }

    public function testUpdateUserSuccessfully(): void
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

        $this->putJson('/api/user/' . $user->json('data.user.id'), [
            'name' => $this->faker->name,
            'email' => 'unittest_' . $this->faker->email,
        ], [
            'Authorization' => 'Bearer ' . $token,
        ])
        ->assertStatus(200);
    }
}
