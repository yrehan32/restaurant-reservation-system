<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Passport::tokensCan([
            'create-user' => 'Create User',
            'read-user' => 'Read User',
            'update-user' => 'Update User',
            'delete-user' => 'Delete User',
            'create-table' => 'Create Table',
            'read-table' => 'Read Table',
            'update-table' => 'Update Table',
            'delete-table' => 'Delete Table',
            'create-booking' => 'Create Booking',
            'read-booking' => 'Read Booking',
            'update-booking' => 'Update Booking',
            'delete-booking' => 'Delete Booking',
            'create-offline-booking' => 'Create Offline Booking',
            'read-offline-booking' => 'Read Offline Booking',
            'update-offline-booking' => 'Update Offline Booking',
            'delete-offline-booking' => 'Delete Offline Booking',
        ]);
    }
}
