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

Route::match(['post', 'get'], 'auth', [AuthController::class, 'auth']);

Route::middleware(['auth:sanctum', 'usage.limit'])->match(['get', 'post'], 'scrape', [ScrapeController::class, 'scrape']);
Route::match(['get', 'post'], 'query', [ScrapeController::class, 'query']);
Route::match(['get', 'post'], 'update', [ScrapeController::class, 'update']);
