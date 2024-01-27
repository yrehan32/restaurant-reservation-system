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

class GetBookingTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('passport:install');
    }

    public function testGetBookingWithoutToken(): void
    {
        $this->getJson('/api/booking')
            ->assertStatus(401);
    }

    public function testGetBookingWithRoleUser(): void
    {
        $user = User::factory()->create();

        Passport::actingAs($user, Constant::USER_SCOPE);

        $this->getJson('/api/booking')
            ->assertStatus(200);
    }

    public function testGetBookingWithRoleAdmin(): void
    {
        $user = User::factory()->create();

        Passport::actingAs($user, Constant::ADMIN_SCOPE);

        $this->getJson('/api/booking')
            ->assertStatus(200);
    }

    public function testGetBookingWithRoleAdminAndId(): void
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

        $this->getJson('/api/booking/' . $booking->json('data.booking.id'))
            ->assertStatus(200);
    }

    public function testGetBookingWithRoleUserAndId(): void
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

        $this->getJson('/api/booking/' . $booking->json('data.booking.id'))
            ->assertStatus(200);
    }

    public function testGetBookingWithRoleUserAndIdIsNotExists(): void
    {
        $user = User::factory()->create();

        Passport::actingAs($user, Constant::USER_SCOPE);

        $this->getJson('/api/booking/9999')
            ->assertStatus(404);
    }

    public function testGetBookingWithRoleAdminAndIdIsNotExists(): void
    {
        $user = User::factory()->create();

        Passport::actingAs($user, Constant::ADMIN_SCOPE);

        $this->getJson('/api/booking/9999')
            ->assertStatus(404);
    }
}
