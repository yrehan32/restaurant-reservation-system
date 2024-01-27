<?php

use App\Http\Controllers\API\Booking;
use App\Http\Controllers\API\OffileBooking;
use App\Http\Controllers\API\Table;
use App\Http\Controllers\API\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'register'], function () {
    Route::post('/admin', [User::class, 'createAdmin']);
    Route::post('/user', [User::class, 'createUser']);
});

Route::group(['prefix' => 'auth'], function () {
        Route::post('/login', [User::class, 'login']);
    
        Route::group(['middleware' => 'auth:api'], function () {
            Route::get('/logout', [User::class, 'logout']);
        });
});

Route::group(['prefix' => 'user'], function () {

    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('/', [User::class, 'get'])->middleware('scope:read-user');
        Route::put('/{id}', [User::class, 'update'])->middleware('scope:update-user');
        Route::delete('/{id}', [User::class, 'delete'])->middleware('scope:delete-user');
    });

    Route::post('/', [User::class, 'create']);
});

Route::group(['prefix' => 'table'], function () {
    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('/', [Table::class, 'get'])->middleware('scope:read-table');
        Route::get('/{id}', [Table::class, 'getById'])->middleware('scope:read-table');
        Route::post('/', [Table::class, 'create'])->middleware('scope:create-table');
        Route::put('/{id}', [Table::class, 'update'])->middleware('scope:update-table');
        Route::delete('/{id}', [Table::class, 'delete'])->middleware('scope:delete-table');
    });
});

Route::group(['prefix' => 'booking'], function () {
    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('/', [Booking::class, 'get'])->middleware('scope:read-booking');
        Route::get('/{id}', [Booking::class, 'getById'])->middleware('scope:read-booking');
        Route::post('/', [Booking::class, 'create'])->middleware('scope:create-booking');
        Route::put('/{id}', [Booking::class, 'update'])->middleware('scope:update-booking');
        Route::delete('/{id}', [Booking::class, 'delete'])->middleware('scope:delete-booking');
    });
});

Route::group(['prefix' => 'offline-booking'], function () {
    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('/', [OffileBooking::class, 'get'])->middleware('scope:read-offline-booking');
        Route::get('/{id}', [OffileBooking::class, 'getById'])->middleware('scope:read-offline-booking');
        Route::post('/', [OffileBooking::class, 'create'])->middleware('scope:create-offline-booking');
        Route::put('/{id}', [OffileBooking::class, 'update'])->middleware('scope:update-offline-booking');
        Route::delete('/{id}', [OffileBooking::class, 'delete'])->middleware('scope:delete-offline-booking');
    });
});
