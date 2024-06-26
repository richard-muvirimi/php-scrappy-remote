<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ScrapeController;
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

Route::match(['post', 'get'], 'login', [AuthController::class, 'login']);
Route::match(['post', 'get'], 'register', [AuthController::class, 'register']);
Route::middleware(['auth:sanctum'])->match(['post', 'get'], 'logout', [AuthController::class, 'logout']);

Route::middleware(['auth:sanctum', 'usage.limit'])->match(['get', 'post'], 'scrape', [ScrapeController::class, 'scrape']);
Route::match(['get', 'post'], 'query', [ScrapeController::class, 'query']);
Route::match(['get', 'post'], 'update', [ScrapeController::class, 'update']);
