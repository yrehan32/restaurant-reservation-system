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

Route::group(['prefix' => 'user'], function () {

    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('/', [User::class, 'get']);
        Route::put('/{id}', [User::class, 'update']);
        Route::delete('/{id}', [User::class, 'delete']);
    });

    Route::post('/', [User::class, 'create']);
});

Route::group(['prefix' => 'table'], function () {
    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('/', [Table::class, 'get']);
        Route::get('/{id}', [Table::class, 'getById']);
        Route::post('/', [Table::class, 'create']);
        Route::put('/{id}', [Table::class, 'update']);
        Route::delete('/{id}', [Table::class, 'delete']);
    });
});

Route::group(['prefix' => 'booking'], function () {
    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('/', [Booking::class, 'get']);
        Route::get('/{id}', [Booking::class, 'getById']);
        Route::post('/', [Booking::class, 'create']);
        Route::put('/{id}', [Booking::class, 'update']);
        Route::delete('/{id}', [Booking::class, 'delete']);
    });
});

Route::group(['prefix' => 'offline-booking'], function () {
    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('/', [OffileBooking::class, 'get']);
        Route::get('/{id}', [OffileBooking::class, 'getById']);
        Route::post('/', [OffileBooking::class, 'create']);
        Route::put('/{id}', [OffileBooking::class, 'update']);
        Route::delete('/{id}', [OffileBooking::class, 'delete']);
    });
});
