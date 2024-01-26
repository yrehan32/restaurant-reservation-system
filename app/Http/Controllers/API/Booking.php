<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Booking\CreateRequest;
use App\Http\Requests\Booking\UpdateRequest;
use App\Services\Booking as ServicesBooking;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Booking extends Controller
{
    public function get() : JsonResponse
    {
        $service = new ServicesBooking('online');

        $bookings = $service->get();

        return response()->json([
            'error' => false,
            'message' => 'Booking retrieved successfully.',
            'data' => [
                'bookings' => $bookings['data'],
            ],
        ]);
    }

    public function getById($id) : JsonResponse
    {
        $isError = false;
        $message = "";
        $data = [];
        $statusCode = 200;

        try {
            $service = new ServicesBooking('online');
    
            $booking = $service->get($id);

            if (!$booking['success']) {
                $statusCode = $booking['status_code'];
                throw new \Exception($booking['message']);
            }

            $message = "Booking retrieved successfully.";
            $data = [
                'booking' => $booking['data'],
            ];
        } catch (\Throwable $th) {
            $isError = true;
            $message = $th->getMessage();
        }

        return response()->json([
            'error' => $isError,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    public function create(CreateRequest $request) : JsonResponse
    {
        $isError = false;
        $message = "";
        $data = [];
        $statusCode = 200;
        
        try {
            $service = new ServicesBooking('online');

            // TODO: Override user_id and status on $request->validated()

            $booking = $service->create($request->validated());

            if (!$booking['success']) {
                $statusCode = $booking['status_code'];
                throw new \Exception($booking['message']);
            }

            $message = "Booking created successfully.";
            $data = [
                'booking' => $booking['data'],
            ];
        } catch (\Throwable $th) {
            $isError = true;
            $message = $th->getMessage();
        }

        return response()->json([
            'error' => $isError,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    public function update(UpdateRequest $request, $id) : JsonResponse
    {
        $isError = false;
        $message = "";
        $data = [];
        $statusCode = 200;
        
        try {
            $service = new ServicesBooking('online');

            $booking = $service->update($request->validated(), $id);

            if (!$booking['success']) {
                $statusCode = $booking['status_code'];
                throw new \Exception($booking['message']);
            }

            $message = "Booking updated successfully.";
            $data = [
                'booking' => $booking['data'],
            ];
        } catch (\Throwable $th) {
            $isError = true;
            $message = $th->getMessage();
        }

        return response()->json([
            'error' => $isError,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    public function delete($id) : JsonResponse
    {
        $isError = false;
        $message = "";
        $data = [];
        $statusCode = 200;
        
        try {
            $service = new ServicesBooking('online');

            $booking = $service->delete($id);

            if (!$booking['success']) {
                $statusCode = $booking['status_code'];
                throw new \Exception($booking['message']);
            }

            $message = "Booking deleted successfully.";
            $data = null;
        } catch (\Throwable $th) {
            $isError = true;
            $message = $th->getMessage();
        }

        return response()->json([
            'error' => $isError,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }
}
