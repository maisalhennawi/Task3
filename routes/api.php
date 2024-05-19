<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
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

$api_path = '/Api/';

Route::prefix('api/auth')->group(function () use ($api_path) {
    include __DIR__ . "{$api_path}Auth.php";
    // Include other route files as needed
});
// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


// Route::prefix('auth')->group(function () {
//     Route::post('signup', [AuthController::class, 'signup']);
//     Route::post('login', [AuthController::class, 'login']);
//     Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
//     Route::post('refresh', [AuthController::class, 'refresh'])->middleware('auth:sanctum');
//     Route::post('verify-email', [AuthController::class, 'verifyEmail']);
// });