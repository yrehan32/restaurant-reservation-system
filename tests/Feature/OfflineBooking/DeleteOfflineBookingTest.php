<?php

namespace Tests\Feature\OfflineBooking;

use App\Models\Table;
use App\Models\User;
use App\Utils\Constant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Tests\TestCase;

class DeleteOfflineBookingTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('passport:install');
    }

    public function testDeleteOfflineBookingWithoutToken(): void
    {
        $this->deleteJson('/api/offline-booking/1')
            ->assertStatus(401);
    }

    public function testDeleteOfflineBookingWithRoleUser(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $user = User::factory()->create();
        $table = Table::factory()->create();
        
        $data = [
            'admin_id' => $admin->id,
            'table_id' => $table->id,
            'booking_time' => '2030-01-02 18:00:00',
            'number_of_people' => 5,
            'customer_name' => $this->faker->name,
            'customer_phone' => $this->faker->phoneNumber,
            'status' => 'accepted',
        ];

        Passport::actingAs($admin, Constant::ADMIN_SCOPE);
        
        $booking = $this->postJson('/api/offline-booking', $data)
            ->assertStatus(200);
        
        Passport::actingAs($user, Constant::USER_SCOPE);

        $this->deleteJson('/api/offline-booking/' . $booking->json('data.booking.id'))
            ->assertStatus(403);
    }

    public function testDeleteOfflineBookingWithRoleAdmin(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $table = Table::factory()->create();
        
        $data = [
            'admin_id' => $admin->id,
            'table_id' => $table->id,
            'booking_time' => '2030-01-02 18:00:00',
            'number_of_people' => 5,
            'customer_name' => $this->faker->name,
            'customer_phone' => $this->faker->phoneNumber,
            'status' => 'accepted',
        ];

        Passport::actingAs($admin, Constant::ADMIN_SCOPE);

        $booking = $this->postJson('/api/offline-booking', $data);

        $this->deleteJson('/api/offline-booking/' . $booking->json('data.booking.id'))
            ->assertStatus(200);
    }

    public function testDeleteOfflineBookingWithBookingNotFound(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);
        
        Passport::actingAs($admin, Constant::ADMIN_SCOPE);

        $this->deleteJson('/api/offline-booking/9999')
            ->assertStatus(404);
    }

    public function testDeleteOfflineBookingWithBookingAlreadyDeleted(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $user = User::factory()->create();
        $table = Table::factory()->create();
        
        $data = [
            'admin_id' => $admin->id,
            'table_id' => $table->id,
            'booking_time' => '2030-01-02 18:00:00',
            'number_of_people' => 5,
            'customer_name' => $this->faker->name,
            'customer_phone' => $this->faker->phoneNumber,
            'status' => 'accepted',
        ];

        Passport::actingAs($admin, Constant::ADMIN_SCOPE);
        
        $booking = $this->postJson('/api/offline-booking', $data)
            ->assertStatus(200);
        
        $this->deleteJson('/api/offline-booking/' . $booking->json('data.booking.id'))
            ->assertStatus(200);

        $this->deleteJson('/api/offline-booking/' . $booking->json('data.booking.id'))
            ->assertStatus(404);
    }
}
