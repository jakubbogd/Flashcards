<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FlashcardController;
use App\Http\Controllers\Api\FlashcardOptionController;
use App\Http\Controllers\Api\SetController;
use App\Http\Controllers\Api\FlashcardImportController;
use App\Http\Controllers\Api\FolderController;
use App\Http\Controllers\Api\ExamController;
use App\Http\Controllers\Api\SmartLearnController;
use App\Http\Controllers\Api\SettingsController;

Route::apiResource('sets', SetController::class);

Route::get('sets/{set}/flashcards', [FlashcardController::class, 'indexBySet']);
Route::post('sets/{set}/flashcards', [FlashcardController::class, 'storeInSet']);
Route::delete('flashcards/{flashcard}/image', [FlashcardController::class, 'deleteImage']);
Route::put('flashcards/{flashcard}/image', [FlashcardController::class, 'updateImage']);
Route::put('option/{option}', [FlashcardOptionController::class, 'update']);
Route::post('flashcards/{flashcard}/option', [FlashcardOptionController::class, 'store']);

Route::apiResource('{set}/flashcards', FlashcardController::class);
Route::put('flashcards/{flashcard}/learned', [FlashcardController::class, 'updateLearned']);

Route::post('{set}/flashcards/import', [FlashcardImportController::class, 'store']);
Route::apiResource('folders', FolderController::class);

Route::prefix('exams')->group(function () {
    Route::post('/start', [ExamController::class, 'start']);
    Route::get('/{uuid}', [ExamController::class, 'show']);
    Route::post('/{uuid}/answer/{order}', [ExamController::class, 'answer']);
});

Route::get('/exams', [ExamController::class, 'index']);

Route::prefix('smart-learn')->group(function () {
    Route::post('/start', [SmartLearnController::class, 'start']);
    Route::get('/{uuid}', [SmartLearnController::class, 'show']);
    Route::post('/{uuid}/answer/{order}', [SmartLearnController::class, 'answer']);
});

Route::get('/settings', [SettingsController::class, 'show']);
Route::put('/settings', [SettingsController::class, 'update']);
