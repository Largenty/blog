<?php

use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\AuthController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('articles', [ArticleController::class, 'index']);
Route::get('articles/{article}', [ArticleController::class, 'show']);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::put('/user/update', [AuthController::class,'updateUser']);
    Route::put('/user/password', [AuthController::class,'modifyPassword']);
    
    Route::delete('articles/{article}', [ArticleController::class, 'destroy']);
    Route::put('articles/{article}', [ArticleController::class, 'update']);
    Route::patch('articles/{article}', [ArticleController::class, 'update']);
    Route::post('articles', [ArticleController::class, 'store']);

});



Route::fallback(function () {
    return response()->json(['message' => 'Route non trouvée'], 404);
});
