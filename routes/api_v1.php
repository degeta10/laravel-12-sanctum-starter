<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\AuthController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:auth')->group(
    function (): void {
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/register', [AuthController::class, 'register']);
    }
);

Route::middleware(['auth:sanctum', 'throttle:authenticated'])->group(function (): void {
    Route::get('/me', [AuthController::class, 'getMe']);
    Route::patch('/me', [AuthController::class, 'updateMe']);
    Route::post('/logout', [AuthController::class, 'logout']);
});
