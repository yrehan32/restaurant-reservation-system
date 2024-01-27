<?php

namespace Tests\Feature\Table;

use App\Models\User;
use App\Utils\Constant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Laravel\Passport\Passport;
use Tests\TestCase;

class DeleteTableTest extends TestCase
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

    public function testDeleteTableWithoutToken(): void
    {
        $this->deleteJson('/api/table/1')
            ->assertStatus(401);
    }

    public function testDeleteTableWithRoleAdmin(): void
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

        $this->deleteJson('/api/table/' . $table->json('data.table.id'), [], [
            'Authorization' => 'Bearer ' . $this->admin_token,
        ])
        ->assertStatus(200);
    }

    public function testDeleteTableWithRoleUser(): void
    {
        $user = User::factory()->create();
        
        $data = [
            'table_number' => 1,
            'capacity' => 4,
            "is_available" => true,
        ];

        $table = $this->postJson('/api/table', $data, [
            'Authorization' => 'Bearer ' . $this->admin_token,
        ])
        ->assertStatus(200);

        Passport::actingAs($user, Constant::USER_SCOPE);

        $this->deleteJson('/api/table/' . $table->json('data.table.id'))
            ->assertStatus(403);
    }
}
