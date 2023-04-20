<?php

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
/*

How about no auth for now?
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

*/

use App\Http\Controllers\ProductController;
use App\Http\Controllers\BookingController;

Route::get('/products', [ProductController::class, 'index']);

Route::get('/bookings', [BookingController::class, 'index']);
Route::post('/bookings', [BookingController::class, 'store']);