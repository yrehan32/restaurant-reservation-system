<?php

namespace Tests\Feature\Table;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UpdateTableTest extends TestCase
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

    public function testUpdateTableWithoutToken(): void
    {
        $data = [
            'table_number' => 1,
            'capacity' => 4,
            "is_available" => true,
        ];

        $this->putJson('/api/table/1', $data)
            ->assertStatus(401);
    }

    public function testUpdateTableWithTableNumberIsString(): void
    {
        $data = [
            'table_number' => 'string',
            'capacity' => 4,
            "is_available" => true,
        ];

        $this->putJson('/api/table/1', $data, [
            'Authorization' => 'Bearer ' . $this->admin_token,
        ])
        ->assertStatus(422);
    }

    public function testUpdateTableSuccessfully(): void
    {
        $data = [
            'table_number' => 1,
            'capacity' => 4,
            "is_available" => true,
        ];

        $table = $this->postJson('/api/table', $data, [
            'Authorization' => 'Bearer ' . $this->admin_token,
        ])
        ->assertStatus(200);

        $data = [
            'table_number' => 2,
            'capacity' => 4,
            "is_available" => true,
        ];

        $this->putJson('/api/table/' . $table->json('data.table.id'), $data, [
            'Authorization' => 'Bearer ' . $this->admin_token,
        ])
        ->assertStatus(200);
    }

    public function testUpdateTableWithTableNumberIsDuplicate(): void
    {
        $data_1 = [
            'table_number' => 1,
            'capacity' => 4,
            "is_available" => true,
        ];

        $table_1 = $this->postJson('/api/table', $data_1, [
            'Authorization' => 'Bearer ' . $this->admin_token,
        ]);

        $data_2 = [
            'table_number' => 2,
            'capacity' => 4,
            "is_available" => true,
        ];

        $table_2 = $this->postJson('/api/table', $data_2, [
            'Authorization' => 'Bearer ' . $this->admin_token,
        ]);

        $this->putJson('/api/table/' . $table_2->json('data.table.id'), $data_1, [
            'Authorization' => 'Bearer ' . $this->admin_token,
        ])
        ->assertStatus(422);
    }

    public function testUpdateTableWithTableNumberIsDuplicateButSameId(): void
    {
        $data = [
            'table_number' => 1,
            'capacity' => 4,
            "is_available" => true,
        ];

        $table = $this->postJson('/api/table', $data, [
            'Authorization' => 'Bearer ' . $this->admin_token,
        ])
        ->assertStatus(200);

        $data = [
            'table_number' => 2,
            'capacity' => 4,
            "is_available" => true,
        ];

        $this->postJson('/api/table', $data, [
            'Authorization' => 'Bearer ' . $this->admin_token,
        ])
        ->assertStatus(200);

        $data = [
            'table_number' => 1,
            'capacity' => 4,
            "is_available" => true,
        ];

        $this->putJson('/api/table/' . $table->json('data.table.id'), $data, [
            'Authorization' => 'Bearer ' . $this->admin_token,
        ])
        ->assertStatus(200);
    }
}
