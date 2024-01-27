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
        $statusCode = 200;

        try {
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
    
            $bookings = $bookings->get();
    
            // If no booking found, throw exception
            if (!empty($id) && $bookings->isEmpty()) {
                $statusCode = 404;
                throw new \Exception('Booking not found.');
            }

            return [
                'success' => true,
                'data' => $bookings->toArray(),
            ];
        } catch (\Throwable $th) {
            return [
                'success' => false,
                'message' => $th->getMessage(),
                'status_code' => $statusCode,
            ];
        }
    }

    public function create(array $data) : array
    {
        $statusCode = 200;

        DB::beginTransaction();

        try {
            // Check for table availability, prevent race condition
            $table = ModelsTable::where('id', $data['table_id'])
                ->lockForUpdate()
                ->first();

            if (!$table->is_available) {
                $statusCode = 409;
                throw new \Exception('Table already booked.');
            }

            // If number_of_people > table capacity, throw exception
            if ($data['number_of_people'] > $table->capacity) {
                $statusCode = 422;
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
                'status_code' => $statusCode,
            ];
        }
    }

    public function update(array $data, int $id) : array
    {
        $statusCode = 200;
        
        DB::beginTransaction();

        try {
            $table = ModelsTable::findOrFail($data['table_id']);

            // If number_of_people > table capacity, throw exception
            if ($data['number_of_people'] > $table->capacity) {
                $statusCode = 422;
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
                'status_code' => $statusCode,
            ];
        }
    }

    public function delete(int $id) : array
    {
        $statusCode = 200;

        DB::beginTransaction();

        try {
            $booking = $this->bookingModel->find($id);

            if (!$booking) {
                $statusCode = 404;
                throw new \Exception('Booking not found.');
            }

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
                'status_code' => $statusCode,
            ];
        }
    }
}