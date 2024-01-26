<?php

namespace Tests\Feature\Booking;

use App\Models\Table;
use App\Models\User;
use App\Utils\Constant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Laravel\Passport\Passport;
use Tests\TestCase;

class UpdateBookingTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('passport:install');
    }

    public function testUpdateBookingWithoutToken(): void
    {
        $this->putJson('/api/booking/1')
            ->assertStatus(401);
    }

    public function testUpdateBookingWithRoleUser(): void
    {
        $user = User::factory()->create();
        $table = Table::factory()->create();
        
        $data = [
            'user_id' => $user->id,
            'table_id' => $table->id,
            'booking_time' => '2030-01-01 18:00:00',
            'number_of_people' => 4,
            'status' => 'pending',
        ];

        Passport::actingAs($user, Constant::USER_SCOPE);

        $booking = $this->postJson('/api/booking', $data)
            ->assertStatus(200);

        $updateData = [
            'user_id' => $user->id,
            'table_id' => $table->id,
            'booking_time' => '2030-01-02 18:00:00',
            'number_of_people' => 4,
            'status' => 'accepted',
        ];

        $this->putJson('/api/booking/' . $booking->json('data.booking.id'), $updateData)
            ->assertStatus(200);
    }

    public function testUpdateBookingWithRoleAdmin(): void
    {
        $user = User::factory()->create();
        $table = Table::factory()->create();
        
        $data = [
            'user_id' => $user->id,
            'table_id' => $table->id,
            'booking_time' => '2030-01-01 18:00:00',
            'number_of_people' => 4,
            'status' => 'pending',
        ];

        Passport::actingAs($user, Constant::ADMIN_SCOPE);

        $booking = $this->postJson('/api/booking', $data)
            ->assertStatus(200);

        $updateData = [
            'user_id' => $user->id,
            'table_id' => $table->id,
            'booking_time' => '2030-01-02 18:00:00',
            'number_of_people' => 4,
            'status' => 'accepted',
        ];

        $this->putJson('/api/booking/' . $booking->json('data.booking.id'), $updateData)
            ->assertStatus(200);
    }

    public function testUpdateBookingWithBookingTimeIsBeforeNow(): void
    {
        $user = User::factory()->create();
        $table = Table::factory()->create();
        
        $data = [
            'user_id' => $user->id,
            'table_id' => $table->id,
            'booking_time' => '2030-01-01 18:00:00',
            'number_of_people' => 4,
            'status' => 'pending',
        ];

        Passport::actingAs($user, Constant::USER_SCOPE);

        $booking = $this->postJson('/api/booking', $data)
            ->assertStatus(200);

        $updateData = [
            'user_id' => $user->id,
            'table_id' => $table->id,
            'booking_time' => '2020-01-02 18:00:00',
            'number_of_people' => 4,
            'status' => 'accepted',
        ];

        $this->putJson('/api/booking/' . $booking->json('data.booking.id'), $updateData)
            ->assertStatus(422);
    }

    public function testUpdateBookingWithNumberOfPeopleExceedsTableCapacity(): void
    {
        $user = User::factory()->create();
        $table = Table::factory()->create([
            'capacity' => 4,
        ]);
        
        $data = [
            'user_id' => $user->id,
            'table_id' => $table->id,
            'booking_time' => '2030-01-01 18:00:00',
            'number_of_people' => 4,
            'status' => 'pending',
        ];

        Passport::actingAs($user, Constant::USER_SCOPE);

        $booking = $this->postJson('/api/booking', $data)
            ->assertStatus(200);

        $updateData = [
            'user_id' => $user->id,
            'table_id' => $table->id,
            'booking_time' => '2030-01-01 18:00:00',
            'number_of_people' => 5,
            'status' => 'pending',
        ];

        $this->putJson('/api/booking/' . $booking->json('data.booking.id'), $updateData)
            ->assertStatus(422);
    }

    public function testUpdateBookingWithTableDoesNotExist(): void
    {
        $user = User::factory()->create();
        $table = Table::factory()->create([
            'capacity' => 4,
        ]);
        
        $data = [
            'user_id' => $user->id,
            'table_id' => $table->id,
            'booking_time' => '2030-01-01 18:00:00',
            'number_of_people' => 4,
            'status' => 'pending',
        ];

        Passport::actingAs($user, Constant::USER_SCOPE);

        $booking = $this->postJson('/api/booking', $data)
            ->assertStatus(200);

        $updateData = [
            'user_id' => $user->id,
            'table_id' => 999,
            'booking_time' => '2030-01-01 18:00:00',
            'number_of_people' => 4,
            'status' => 'pending',
        ];

        $this->putJson('/api/booking/' . $booking->json('data.booking.id'), $updateData)
            ->assertStatus(422);
    }
}
