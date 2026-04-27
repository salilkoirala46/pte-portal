<?php

use App\Http\Controllers\Api\AudioController;
use App\Http\Controllers\Api\QuestionApiController;
use App\Http\Controllers\Api\ScoringController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'tenant'])->group(function () {

    // Audio upload for speaking submissions
    Route::post('/audio/upload',       [AudioController::class, 'upload'])->name('api.audio.upload');
    Route::get('/audio/{recording}',   [AudioController::class, 'stream'])->name('api.audio.stream');

    // Questions
    Route::get('/questions/{question}/audio', [QuestionApiController::class, 'streamAudio'])->name('api.questions.audio');

    // Scoring
    Route::post('/score/speaking',  [ScoringController::class, 'scoreSpeeking'])->name('api.score.speaking');
    Route::post('/score/writing',   [ScoringController::class, 'scoreWriting'])->name('api.score.writing');
});
