<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('offline_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')
                ->constrained('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreignId('table_id')
                ->constrained('tables')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->dateTime('booking_time');
            $table->integer('number_of_people');
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('status')
                ->default('accepted')
                ->comment('pending, accepted, rejected, canceled, completed');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offline_bookings');
    }
};
