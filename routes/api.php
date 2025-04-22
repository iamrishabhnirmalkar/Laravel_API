<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Spatie\Health\Http\Controllers\HealthCheckResultsController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::get('/', function () {
    return response()->json([
        'message' => 'Welcome to the E-Commerce API 🚀',
        'status' => 'success',
    ]);
});

Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'API endpoint not found.'
    ], 404);
});

Route::prefix('v1')->middleware(['throttle:60,1'])->group(function () {

    // Testing Api
    Route::get('/test', function () {
        return response()->json(['message' => 'API is working']);
    });
    Route::get('/health', HealthCheckResultsController::class);


    // Auth API

    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
        Route::post('logout', [AuthController::class, 'logout'])->middleware('jwt.auth');
        Route::post('refresh', [AuthController::class, 'refresh'])->middleware('jwt.auth');
        Route::get('me', [AuthController::class, 'me'])->middleware('jwt.auth');
    });

}); 