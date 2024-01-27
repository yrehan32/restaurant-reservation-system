<?php

namespace Tests\Feature\Booking;

use App\Models\Table;
use App\Models\User as ModelsUser;
use App\Utils\Constant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Laravel\Passport\Passport;
use Tests\TestCase;

class CreateBookingTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('passport:install');
    }

    public function testCreateBookingWithoutToken(): void
    {
        $user = ModelsUser::factory()->make([
            'role' => 'admin',
        ]);

        $table = Table::factory()->create();

        $data = [
            'user_id' => $user->id,
            'table_id' => $table->id,
            'booking_time' => '2030-01-01 18:00:00',
            'number_of_people' => 4,
            'status' => 'pending',
        ];

        $this->postJson('/api/booking', $data)
            ->assertStatus(401);
    }

    public function testCreateBookingWithRoleAdmin(): void
    {
        $user = ModelsUser::factory()->create();
        $table = Table::factory()->create();
        
        $data = [
            'user_id' => $user->id,
            'table_id' => $table->id,
            'booking_time' => '2030-01-01 18:00:00',
            'number_of_people' => 3,
            'status' => 'pending',
        ];

        Passport::actingAs($user, Constant::ADMIN_SCOPE);
        
        $this->postJson('/api/booking', $data)
            ->assertStatus(200);
    }

    public function testCreateBookingWithRoleUser(): void
    {
        $user = ModelsUser::factory()->create();
        $table = Table::factory()->create();
        
        $data = [
            'user_id' => $user->id,
            'table_id' => $table->id,
            'booking_time' => '2030-01-01 18:00:00',
            'number_of_people' => 3,
            'status' => 'pending',
        ];

        Passport::actingAs($user, Constant::USER_SCOPE);
        
        $this->postJson('/api/booking', $data)
            ->assertStatus(200);
    }

    public function testCreateBookingWithRoleUserAndTableIsNotAvailable(): void
    {
        $user = ModelsUser::factory()->create();
        $table = Table::factory()->create([
            'is_available' => false,
        ]);
        
        $data = [
            'user_id' => $user->id,
            'table_id' => $table->id,
            'booking_time' => '2030-01-01 18:00:00',
            'number_of_people' => 4,
            'status' => 'pending',
        ];

        Passport::actingAs($user, Constant::USER_SCOPE);
        
        $this->postJson('/api/booking', $data)
            ->assertStatus(409);
    }

    public function testCreateBookingWithRoleUserAndBookingTimeIsBeforeNow(): void
    {
        $user = ModelsUser::factory()->create();
        $table = Table::factory()->create();
        
        $data = [
            'user_id' => $user->id,
            'table_id' => $table->id,
            'booking_time' => '2020-01-01 18:00:00',
            'number_of_people' => 4,
            'status' => 'pending',
        ];

        Passport::actingAs($user, Constant::USER_SCOPE);
        
        $this->postJson('/api/booking', $data)
            ->assertStatus(422);
    }

    public function testCreateBookingWithRoleUserAndNumberOfPeopleExceedsTableCapacity(): void
    {
        $user = ModelsUser::factory()->create();
        $table = Table::factory()->create([
            'capacity' => 2,
        ]);
        
        $data = [
            'user_id' => $user->id,
            'table_id' => $table->id,
            'booking_time' => '2030-01-01 18:00:00',
            'number_of_people' => 4,
            'status' => 'pending',
        ];

        Passport::actingAs($user, Constant::USER_SCOPE);
        
        $this->postJson('/api/booking', $data)
            ->assertStatus(422);
    }

    public function testCreateBookingWithRoleUserAndTableDoesNotExist(): void
    {
        $user = ModelsUser::factory()->create();
        
        $data = [
            'user_id' => $user->id,
            'table_id' => 999,
            'booking_time' => '2030-01-01 18:00:00',
            'number_of_people' => 4,
            'status' => 'pending',
        ];

        Passport::actingAs($user, Constant::USER_SCOPE);
        
        $this->postJson('/api/booking', $data)
            ->assertStatus(422);
    }

    public function testCreateBookingWithRoleUserAndUserDoesNotExist(): void
    {
        $user = ModelsUser::factory()->create();
        $table = Table::factory()->create();
        
        $data = [
            'user_id' => 999,
            'table_id' => $table->id,
            'booking_time' => '2030-01-01 18:00:00',
            'number_of_people' => 4,
            'status' => 'pending',
        ];

        Passport::actingAs($user, Constant::USER_SCOPE);
        
        $this->postJson('/api/booking', $data)
            ->assertStatus(422);
    }

    // public function testRaceCondition()
    // {
    //     $booking_data = [
    //         'date' => '2022-01-01',
    //         'time' => '18:00:00',
    //         'party_size' => 4,
    //     ];

    //     $booking_count = 10;

    //     // Create multiple bookings simultaneously
    //     $responses = [];
    //     $runtime = new Runtime();
    //     $future = [];

    //     for ($i = 0; $i < $booking_count; $i++) {
    //         $future[$i] = $runtime->run(function () use ($booking_data) {
    //             $response = $this->postJson('/api/bookings', $booking_data, [
    //                 'Authorization' => 'Bearer ' . $this->user_token,
    //             ]);
    //             return $response;
    //         });
    //     }

    //     // Collect the results from all futures
    //     foreach ($future as $index => $f) {
    //         $responses[$index] = $f->value();
    //     }

    //     // Assert that all bookings were created successfully
    //     foreach ($responses as $response) {
    //         $response->assertStatus(201);
    //     }
    // }
}
