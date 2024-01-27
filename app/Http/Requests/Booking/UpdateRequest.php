<?php

namespace App\Http\Requests\Booking;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => 'required|integer|exists:users,id',
            'table_id' => 'required|integer|exists:tables,id',
            'booking_time' => 'required|date|after:now',
            'number_of_people' => 'required|integer',
            'status' => 'required|string|in:pending,accepted,rejected,canceled,completed',
        ];
    }
}
