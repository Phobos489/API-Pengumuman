<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PengumumanController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Public routes
Route::prefix('v1')->group(function () {
    // Get published announcements
    Route::get('pengumumans/published', [PengumumanController::class, 'published']);
    
    // Get announcements by kategori
    Route::get('pengumumans/kategori/{kategori}', [PengumumanController::class, 'byKategori']);
    
    // CRUD routes
    Route::apiResource('pengumumans', PengumumanController::class);
});