<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfflineBooking extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'admin_id',
        'table_id',
        'booking_time',
        'number_of_people',
        'customer_name',
        'customer_phone',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'admin_id' => 'integer',
        'table_id' => 'integer',
        'booking_time' => 'datetime',
        'number_of_people' => 'integer',
        'customer_name' => 'string',
        'customer_phone' => 'string',
        'status' => 'string',
    ];

    /**
     * Get the admin that owns the offline booking.
     */
    public function admin()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the table that owns the offline booking.
     */
    public function table()
    {
        return $this->belongsTo(Table::class);
    }
}
