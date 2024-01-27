<?php

namespace Tests\Feature\Table;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GetTableTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private $admin_token;
    private $user_token;

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('passport:install');

        $admin_data = [
            'name' => $this->faker->name,
            'email' => 'unittest_' . $this->faker->email,
            'password' => 'Password!@#123',
            'password_confirmation' => 'Password!@#123',
            'role' => 'admin',
        ];

        $user_data = [
            'name' => $this->faker->name,
            'email' => 'unittest_' . $this->faker->email,
            'password' => 'Password!@#123',
            'password_confirmation' => 'Password!@#123',
            'role' => 'user',
        ];

        $admin = $this->postJson('/api/user', $admin_data);
        $user = $this->postJson('/api/user', $user_data);

        $this->admin_token = $admin->json('data.token');
        $this->user_token = $user->json('data.token');
    }

    public function testGetTableWithoutToken(): void
    {
        $this->getJson('/api/table')
            ->assertStatus(401);
    }

    public function testGetTableWithRoleAdmin(): void
    {
        $data = [
            'table_number' => 1,
            'capacity' => 4,
            "is_available" => true,
        ];

        $this->postJson('/api/table', $data, [
            'Authorization' => 'Bearer ' . $this->admin_token,
        ])
        ->assertStatus(200);

        $this->getJson('/api/table', [
            'Authorization' => 'Bearer ' . $this->admin_token,
        ])
        ->assertStatus(200);
    }

    public function testGetTableWithRoleUser(): void
    {
        $data = [
            'table_number' => 1,
            'capacity' => 4,
            "is_available" => true,
        ];

        $this->postJson('/api/table', $data, [
            'Authorization' => 'Bearer ' . $this->admin_token,
        ])
        ->assertStatus(200);

        $this->getJson('/api/table', [
            'Authorization' => 'Bearer ' . $this->user_token,
        ])
        ->assertStatus(200);
    }

    public function testGetTableSuccessfully(): void
    {
        $data = [
            'table_number' => 1,
            'capacity' => 4,
            "is_available" => true,
        ];

        $this->postJson('/api/table', $data, [
            'Authorization' => 'Bearer ' . $this->admin_token,
        ])
        ->assertStatus(200);

        $this->getJson('/api/table', [
            'Authorization' => 'Bearer ' . $this->admin_token,
        ])
        ->assertStatus(200);
    }
}
