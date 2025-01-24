<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\Admin\UserController;


// Routes publiques (non protégées)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/leaderboard', [GameController::class, 'getLeaderboard']);

// Routes protégées (authentifiées via Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [ProfileController::class, 'showProfile']);
    Route::post('/profile/update', [ProfileController::class, 'updateProfile']);
    Route::post('/validate-password', [ProfileController::class, 'validatePassword']);
});

// Routes admin (protégées par le middleware 'admin')
Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    Route::get('/users', [UserController::class, 'getAllUsers']);
    Route::post('/users/{id}/update-role', [UserController::class, 'updateUserRole']);
    Route::post('/users/{id}/update-vip', [UserController::class, 'updateUserVIP']);
    Route::post('/users/{id}/toggle-ban', [UserController::class, 'toggleBanUser']);
    Route::delete('/users/{id}', [UserController::class, 'deleteUser']);
});






