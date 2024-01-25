<?php

namespace Tests\Feature\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateUserTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('passport:install');
    }

    public function testCreateUserErrorValidation(): void
    {
        $this->postJson('/api/user', [])
            ->assertStatus(422);
    }

    public function testCreateUserSuccessfully(): void
    {
        $data = [
            'name' => $this->faker->name,
            'email' => 'unittest_' . $this->faker->email,
            'password' => 'Password!@#123',
            'password_confirmation' => 'Password!@#123',
            'role' => 'admin',
        ];

        $this->postJson('/api/user', $data)
            ->assertStatus(200);
    }
}
