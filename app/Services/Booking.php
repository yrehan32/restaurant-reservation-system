<?php

namespace App\Services;

use App\Models\Booking as ModelsBooking;
use App\Models\OfflineBooking as ModelsOfflineBooking;
use App\Models\Table as ModelsTable;
use Illuminate\Support\Facades\DB;

class Booking
{
    private $bookingType;
    private $bookingModel;

    public function __construct($bookingType)
    {
        // Set booking type
        $this->bookingType = $bookingType;

        // Determine booking model
        if ($bookingType === 'online') {
            $this->bookingModel = new ModelsBooking();
        } else {
            $this->bookingModel = new ModelsOfflineBooking();
        }
    }

    public function get(int $id = null, string $status = null) : array
    {
        $bookings = $this->bookingModel;
        
        if ($this->bookingType === 'online') {
            $bookings = $bookings->with('user', 'table');
        } else {
            $bookings = $bookings->with('admin', 'table');
        }

        if ($id) {
            $bookings->where('id', $id);
        }

        if ($status) {
            $bookings->where('status', $status);
        }

        return [
            'success' => true,
            'data' => $bookings->get()->toArray(),
        ];
    }

    public function create(array $data) : array
    {
        DB::beginTransaction();

        try {
            // Check for table availability, prevent race condition
            $table = ModelsTable::where('id', $data['table_id'])
                ->lockForUpdate()
                ->first();

            if (!$table->is_available) {
                throw new \Exception('Table already booked.');
            }

            // If number_of_people > table capacity, throw exception
            if ($data['number_of_people'] > $table->capacity) {
                throw new \Exception('Number of people exceeds table capacity.');
            }

            // Create booking
            $booking = $this->bookingModel->create($data);

            // Update table availability
            $table->update([
                'is_available' => false,
            ]);

            DB::commit();
    
            return [
                'success' => true,
                'data' => $booking->toArray(),
            ];
        } catch (\Throwable $th) {
            DB::rollBack();

            return [
                'success' => false,
                'message' => $th->getMessage(),
            ];
        }
    }

    public function update(array $data, int $id) : array
    {
        DB::beginTransaction();

        try {
            $table = ModelsTable::findOrFail($data['table_id']);

            // If number_of_people > table capacity, throw exception
            if ($data['number_of_people'] > $table->capacity) {
                throw new \Exception('Number of people exceeds table capacity.');
            }

            $booking = $this->bookingModel->findOrFail($id);

            $booking->update($data);

            DB::commit();

            return [
                'success' => true,
                'data' => $booking->toArray(),
            ];
        } catch (\Throwable $th) {
            DB::rollBack();

            return [
                'success' => false,
                'message' => $th->getMessage(),
            ];
        }
    }

    public function delete(int $id) : array
    {
        DB::beginTransaction();

        try {
            $booking = $this->bookingModel->findOrFail($id);

            $booking->delete();

            DB::commit();

            return [
                'success' => true,
                'data' => $booking->toArray(),
            ];
        } catch (\Throwable $th) {
            DB::rollBack();

            return [
                'success' => false,
                'message' => $th->getMessage(),
            ];
        }
    }
}