<?php

namespace Tests\Feature\Booking;

use App\Models\Table;
use App\Models\User;
use App\Utils\Constant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Tests\TestCase;

class DeleteBookingTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('passport:install');
    }

    public function testDeleteBookingWithoutToken(): void
    {
        $this->deleteJson('/api/booking/1')
            ->assertStatus(401);
    }

    public function testDeleteBookingWithRoleUser(): void
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

        $this->deleteJson('/api/booking/' . $booking->json('data.booking.id'))
            ->assertStatus(200);
    }

    public function testDeleteBookingWithRoleAdmin(): void
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

        $this->deleteJson('/api/booking/' . $booking->json('data.booking.id'))
            ->assertStatus(200);
    }

    public function testDeleteBookingWithBookingNotFound(): void
    {
        $user = User::factory()->create();
        
        Passport::actingAs($user, Constant::USER_SCOPE);

        $this->deleteJson('/api/booking/9999')
            ->assertStatus(404);
    }

    public function testDeleteBookingWithBookingAlreadyDeleted(): void
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

        $this->deleteJson('/api/booking/' . $booking->json('data.booking.id'))
            ->assertStatus(200);

        $this->deleteJson('/api/booking/' . $booking->json('data.booking.id'))
            ->assertStatus(404);
    }
}
