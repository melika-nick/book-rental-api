<?php

use App\Http\Controllers\Admin\BookController;
use App\Http\Controllers\Admin\RentalController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::apiResource('books', BookController::class);

//Route::prefix('admin')->group(function () {
//    Route::apiResource('rentals', RentalController::class)->only([
//        'index', 'store', 'show'
//    ]);
//    Route::post('rentals/{rental}/return', [RentalController::class, 'returnBook'])
//        ->name('rentals.return');
//});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// محافظت با Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // مثال: متدهای رنتال فقط بعد از لاگین قابل دسترسی هستند
    Route::apiResource('rentals', RentalController::class)->only([
        'index', 'store', 'show'
    ]);
    Route::post('/rentals/{id}/return', [RentalController::class, 'returnBook']);
});
