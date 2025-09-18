<?php

use App\Http\Controllers\Admin\BookController;
use App\Http\Controllers\Admin\RentalController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
*/

// مسیرهای عمومی برای ثبت‌نام و ورود
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Logout برای تمام کاربران
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

// --------------------
// مسیرهای ادمین
// --------------------
Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group(function () {
    // مدیریت کتاب‌ها
    Route::apiResource('books', BookController::class);

    // مدیریت رنتال‌ها
    Route::apiResource('rentals', RentalController::class)->only([
        'index', 'store', 'show'
    ]);

    // مدیریت کاربران
    Route::apiResource('users', UserController::class);
});

// --------------------
// مسیرهای ممبر
// --------------------
Route::middleware(['auth:sanctum', 'role:member'])->group(function () {
    // مشاهده کتاب‌ها
    Route::get('books', [BookController::class, 'index']);

    //ایجاد و بازگرداندن رنتال
    Route::post('rentals', [RentalController::class, 'store']);
    Route::post('rentals/{id}/return', [RentalController::class, 'returnBook']);
});
