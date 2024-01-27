<?php

namespace Tests\Feature\OfflineBooking;

use App\Models\Table;
use App\Models\User;
use App\Utils\Constant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Tests\TestCase;

class CreateOfflineBookingTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('passport:install');
    }

    public function testCreateOfflineBookingWithoutToken(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $table = Table::factory()->create();

        $data = [
            'admin_id' => $admin->id,
            'table_id' => $table->id,
            'booking_time' => '2030-01-01 18:00:00',
            'number_of_people' => 4,
            'customer_name' => $this->faker->name,
            'customer_phone' => $this->faker->phoneNumber,
            'status' => 'pending',
        ];

        $this->postJson('/api/offline-booking', $data)
            ->assertStatus(401);
    }

    public function testCreateOfflineBookingWithRoleAdmin(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);
        $table = Table::factory()->create();
        
        $data = [
            'admin_id' => $admin->id,
            'table_id' => $table->id,
            'booking_time' => '2030-01-01 18:00:00',
            'number_of_people' => 4,
            'customer_name' => $this->faker->name,
            'customer_phone' => $this->faker->phoneNumber,
            'status' => 'pending',
        ];

        Passport::actingAs($admin, Constant::ADMIN_SCOPE);
        
        $this->postJson('/api/offline-booking', $data)
            ->assertStatus(200);
    }

    public function testCreateOfflineBookingWithRoleUser(): void
    {
        $user = User::factory()->create();
        $table = Table::factory()->create();
        
        $data = [
            'admin_id' => $user->id,
            'table_id' => $table->id,
            'booking_time' => '2030-01-01 18:00:00',
            'number_of_people' => 4,
            'customer_name' => $this->faker->name,
            'customer_phone' => $this->faker->phoneNumber,
            'status' => 'pending',
        ];

        Passport::actingAs($user, Constant::USER_SCOPE);
        
        $this->postJson('/api/offline-booking', $data)
            ->assertStatus(403);
    }

    public function testCreateOfflineBookingWithTableIsNotAvailable(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $table = Table::factory()->create([
            'is_available' => false,
        ]);
        
        $data = [
            'admin_id' => $admin->id,
            'table_id' => $table->id,
            'booking_time' => '2030-01-01 18:00:00',
            'number_of_people' => 4,
            'customer_name' => $this->faker->name,
            'customer_phone' => $this->faker->phoneNumber,
            'status' => 'pending',
        ];

        Passport::actingAs($admin, Constant::ADMIN_SCOPE);
        
        $this->postJson('/api/offline-booking', $data)
            ->assertStatus(409);
    }

    public function testCreateOfflineBookingWithBookingTimeIsBeforeNow(): void
    {
        $admin = User::factory()->create([
            'role' => 'user',
        ]);

        $table = Table::factory()->create();
        
        $data = [
            'admin_id' => $admin->id,
            'table_id' => $table->id,
            'booking_time' => '2020-01-01 18:00:00',
            'number_of_people' => 4,
            'customer_name' => $this->faker->name,
            'customer_phone' => $this->faker->phoneNumber,
            'status' => 'pending',
        ];

        Passport::actingAs($admin, Constant::ADMIN_SCOPE);
        
        $this->postJson('/api/offline-booking', $data)
            ->assertStatus(422);
    }

    public function testCreateOfflineBookingWithNumberOfPeopleExceedsTableCapacity(): void
    {
        $admin = User::factory()->create([
            'role' => 'user',
        ]);

        $table = Table::factory()->create([
            'capacity' => 2,
        ]);
        
        $data = [
            'admin_id' => $admin->id,
            'table_id' => $table->id,
            'booking_time' => '2020-01-01 18:00:00',
            'number_of_people' => 4,
            'customer_name' => $this->faker->name,
            'customer_phone' => $this->faker->phoneNumber,
            'status' => 'pending',
        ];

        Passport::actingAs($admin, Constant::ADMIN_SCOPE);
        
        $this->postJson('/api/offline-booking', $data)
            ->assertStatus(422);
    }

    public function testCreateOfflineBookingWithTableDoesNotExist(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $data = [
            'admin_id' => $admin->id,
            'table_id' => '9999',
            'booking_time' => '2020-01-01 18:00:00',
            'number_of_people' => 4,
            'customer_name' => $this->faker->name,
            'customer_phone' => $this->faker->phoneNumber,
            'status' => 'pending',
        ];

        Passport::actingAs($admin, Constant::ADMIN_SCOPE);
        
        $this->postJson('/api/offline-booking', $data)
            ->assertStatus(422);
    }

    public function testCreateOfflineBookingWithUserDoesNotExist(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $table = Table::factory()->create();
        
        $data = [
            'admin_id' => $admin->id,
            'table_id' => '9999',
            'booking_time' => '2020-01-01 18:00:00',
            'number_of_people' => 4,
            'customer_name' => $this->faker->name,
            'customer_phone' => $this->faker->phoneNumber,
            'status' => 'pending',
        ];

        Passport::actingAs($admin, Constant::ADMIN_SCOPE);
        
        $this->postJson('/api/offline-booking', $data)
            ->assertStatus(422);
    }
}
