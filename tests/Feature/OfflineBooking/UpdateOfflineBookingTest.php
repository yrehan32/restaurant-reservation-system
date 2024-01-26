<?php

namespace Tests\Feature\OfflineBooking;

use App\Models\Table;
use App\Models\User;
use App\Utils\Constant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Laravel\Passport\Passport;
use Tests\TestCase;

class UpdateOfflineBookingTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('passport:install');
    }

    public function testUpdateOfflineBookingWithoutToken(): void
    {
        $this->putJson('/api/offline-booking/1')
            ->assertStatus(401);
    }

    public function testUpdateOfflineBookingWithRoleUser(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $user = User::factory()->create();
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
        
        $booking = $this->postJson('/api/offline-booking', $data);
        
        Passport::actingAs($user, Constant::USER_SCOPE);

        $updateData = [
            'admin_id' => $user->id,
            'table_id' => $table->id,
            'booking_time' => '2030-01-01 18:00:00',
            'number_of_people' => 4,
            'customer_name' => $this->faker->name,
            'customer_phone' => $this->faker->phoneNumber,
            'status' => 'pending',
        ];

        $this->putJson('/api/offline-booking/' . $booking->json('data.booking.id'), $updateData)
            ->assertStatus(403);
    }

    public function testUpdateOfflineBookingWithRoleAdmin(): void
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
            'status' => 'accepted',
        ];

        Passport::actingAs($admin, Constant::ADMIN_SCOPE);

        $booking = $this->postJson('/api/offline-booking', $data);

        $updateData = [
            'admin_id' => $admin->id,
            'table_id' => $table->id,
            'booking_time' => '2030-01-02 18:00:00',
            'number_of_people' => 4,
            'customer_name' => $this->faker->name,
            'customer_phone' => $this->faker->phoneNumber,
            'status' => 'pending',
        ];

        $this->putJson('/api/offline-booking/' . $booking->json('data.booking.id'), $updateData)
            ->assertStatus(200);
    }

    public function testUpdateOfflineBookingWithBookingTimeIsBeforeNow(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $table = Table::factory()->create();
        
        $data = [
            'admin_id' => $admin->id,
            'table_id' => $table->id,
            'booking_time' => '2030-01-02 18:00:00',
            'number_of_people' => 4,
            'customer_name' => $this->faker->name,
            'customer_phone' => $this->faker->phoneNumber,
            'status' => 'pending',
        ];

        Passport::actingAs($admin, Constant::ADMIN_SCOPE);

        $booking = $this->postJson('/api/offline-booking', $data);

        $updateData = [
            'admin_id' => $admin->id,
            'table_id' => $table->id,
            'booking_time' => '2020-01-02 18:00:00',
            'number_of_people' => 4,
            'customer_name' => $this->faker->name,
            'customer_phone' => $this->faker->phoneNumber,
            'status' => 'pending',
        ];

        $this->putJson('/api/offline-booking/' . $booking->json('data.booking.id'), $updateData)
            ->assertStatus(422);
    }

    public function testUpdateOfflineBookingWithNumberOfPeopleExceedsTableCapacity(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $table = Table::factory()->create([
            'capacity' => 4,
        ]);
        
        $data = [
            'admin_id' => $admin->id,
            'table_id' => $table->id,
            'booking_time' => '2030-01-02 18:00:00',
            'number_of_people' => 4,
            'customer_name' => $this->faker->name,
            'customer_phone' => $this->faker->phoneNumber,
            'status' => 'accepted',
        ];

        Passport::actingAs($admin, Constant::ADMIN_SCOPE);

        $booking = $this->postJson('/api/offline-booking', $data);

        $updateData = [
            'admin_id' => $admin->id,
            'table_id' => $table->id,
            'booking_time' => '2030-01-02 18:00:00',
            'number_of_people' => 5,
            'customer_name' => $this->faker->name,
            'customer_phone' => $this->faker->phoneNumber,
            'status' => 'accepted',
        ];

        $this->putJson('/api/offline-booking/' . $booking->json('data.booking.id'), $updateData)
            ->assertStatus(422);
    }

    public function testUpdateOfflineBookingWithTableDoesNotExist(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);
        
        $table = Table::factory()->create([
            'capacity' => 4,
        ]);
        
        $data = [
            'admin_id' => $admin->id,
            'table_id' => $table->id,
            'booking_time' => '2030-01-02 18:00:00',
            'number_of_people' => 4,
            'customer_name' => $this->faker->name,
            'customer_phone' => $this->faker->phoneNumber,
            'status' => 'accepted',
        ];

        Passport::actingAs($admin, Constant::ADMIN_SCOPE);

        $booking = $this->postJson('/api/offline-booking', $data);

        $updateData = [
            'admin_id' => $admin->id,
            'table_id' => '9999',
            'booking_time' => '2030-01-02 18:00:00',
            'number_of_people' => 5,
            'customer_name' => $this->faker->name,
            'customer_phone' => $this->faker->phoneNumber,
            'status' => 'accepted',
        ];

        $this->putJson('/api/offline-booking/' . $booking->json('data.booking.id'), $updateData)
            ->assertStatus(422);
    }
}
