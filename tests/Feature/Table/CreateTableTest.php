<?php

namespace Tests\Feature\Table;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateTableTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('passport:install');
    }

    public function testCreateTableWithoutToken(): void
    {
        $data = [
            'table_number' => 1,
            'capacity' => 4,
            "is_available" => true,
        ];

        $this->postJson('/api/table', $data)
            ->assertStatus(401);
    }

    public function testCreateTableWithRoleUser(): void
    {
        $data = [
            'name' => $this->faker->name,
            'email' => 'unittest_' . $this->faker->email,
            'password' => 'Password!@#123',
            'password_confirmation' => 'Password!@#123',
            'role' => 'user',
        ];

        $user = $this->postJson('/api/user', $data);

        $token = $user->json('data.token');

        $data = [
            'table_number' => 1,
            'capacity' => 4,
            "is_available" => true,
        ];

        $this->postJson('/api/table', $data, [
            'Authorization' => 'Bearer ' . $token,
        ])
        ->assertStatus(403);
    }

    public function testCreateTableWithTableNumberIsString(): void
    {
        $data = [
            'name' => $this->faker->name,
            'email' => 'unittest_' . $this->faker->email,
            'password' => 'Password!@#123',
            'password_confirmation' => 'Password!@#123',
            'role' => 'admin',
        ];

        $user = $this->postJson('/api/user', $data)
            ->assertStatus(200);

        $token = $user->json('data.token');

        $data = [
            'table_number' => 1,
            'capacity' => 4,
            "is_available" => true,
        ];

        $data = [
            'table_number' => 'string',
            'capacity' => 4,
            "is_available" => true,
        ];

        $this->postJson('/api/table', $data, [
            'Authorization' => 'Bearer ' . $token,
        ])
        ->assertStatus(422);
    }

    public function testCreateTableSuccessfully(): void
    {
        $data = [
            'name' => $this->faker->name,
            'email' => 'unittest_' . $this->faker->email,
            'password' => 'Password!@#123',
            'password_confirmation' => 'Password!@#123',
            'role' => 'admin',
        ];

        $user = $this->postJson('/api/user', $data)
            ->assertStatus(200);

        $token = $user->json('data.token');

        $data = [
            'table_number' => 1,
            'capacity' => 4,
            "is_available" => true,
        ];

        $this->postJson('/api/table', $data, [
            'Authorization' => 'Bearer ' . $token,
        ])
        ->assertStatus(200);
    }
}
