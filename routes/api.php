<?php

// use App\Http\Controllers\DownloadController;
// use App\Http\Controllers\UploadController;
// use Illuminate\Support\Facades\Route;


use App\Http\Controllers\DownloadController;
use App\Http\Controllers\UploadController;
// use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Route;


// Upload endpoint
// Route::middleware('api')->group(function () {
    // Test endpoint to verify API routes are working
    // Route::get('/test', [TestController::class, 'test']);

    // Upload endpoint
    Route::post('/api/upload', [UploadController::class, 'upload']);

    // Download endpoint
    Route::get('/api/download/{token}', [DownloadController::class, 'download']);

    // Stats endpoint
    Route::get('/api/uploads/stats/{token}', [UploadController::class, 'stats']);

    Route::get('/p', function () {
        return response()->json([
            'status' => 'success',
            'message' => 'Post created successfully'
        ], 201);
    });
// });


Route::get('/', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'Post created successfully'
    ], 201);
});