<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return response()->json([
        'message' => 'API Pengumuman',
        'version' => '1.0',
        'endpoints' => [
            'GET /api/v1/pengumumans' => 'Get all pengumumans',
            'GET /api/v1/pengumumans/{id}' => 'Get single pengumuman',
            'POST /api/v1/pengumumans' => 'Create pengumuman',
            'PUT /api/v1/pengumumans/{id}' => 'Update pengumuman',
            'DELETE /api/v1/pengumumans/{id}' => 'Delete pengumuman',
            'GET /api/v1/pengumumans/published' => 'Get published pengumumans',
            'GET /api/v1/pengumumans/kategori/{kategori}' => 'Get by kategori',
        ]
    ]);
});

// Serve images from assets folder
Route::get('/assets/{filename}', function ($filename) {
    $path = env('UPLOAD_PATH', '/var/www/assets') . '/' . $filename;
    
    if (!file_exists($path)) {
        abort(404);
    }
    
    $file = file_get_contents($path);
    $type = mime_content_type($path);
    
    return response($file, 200)->header('Content-Type', $type);
});