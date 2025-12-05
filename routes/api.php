<?php

use App\Http\Controllers\Customer\AiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| API routes untuk fitur AI dan layanan lainnya
|
*/

Route::prefix('ai')->group(function () {
    // Analisa mood dan rekomendasi
    Route::post('/recommend', [AiController::class, 'recommend']);
    
    // Chat dengan AI assistant
    Route::post('/chat', [AiController::class, 'chat']);
    
    // Quick replies suggestions
    Route::get('/quick-replies', [AiController::class, 'quickReplies']);
    
    // Conversation history
    Route::get('/history', [AiController::class, 'history']);
});
