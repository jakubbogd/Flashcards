<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SettingsController;
use App\Http\Controllers\Api\FlashcardController;
use App\Http\Controllers\Api\FlashcardOptionController;
use App\Http\Controllers\Api\SetController;
use App\Http\Controllers\Api\FlashcardImportController;
use App\Http\Controllers\Api\FolderController;
use App\Http\Controllers\Api\ExamController;
use App\Http\Controllers\Api\SmartLearnController;


Route::middleware(['auth'])->group(function () {
    Route::get('/', function () {
        return view('home');
    });

    Route::get('/create', function () {
        return view('flashcards.create');
    });

    Route::get('/learn', function () {
        return view('flashcards.learn');
    });

    Route::get('/seesets', function () {
        return view('flashcards.sets');
    });


    Route::get('/seesmart-learn', function () {
        return view('smart-learn.start');
    });

    Route::get('/seesmart-learn/{uuid}', function () {
        return view('smart-learn.session');
    });

    Route::get('/exam', fn () => view('exam.start'));
    Route::get('/exam/{uuid}', fn () => view('exam.session'));
    Route::get('/exam/{uuid}/result', fn () => view('exam.result'));
    Route::get('/seeexams', fn () => view('exam.history'));
    Route::get('/seesettings', fn () => view('settings'));

    Route::get('/user', [SettingsController::class, 'user']);
    Route::resource('sets', SetController::class);

    Route::get('sets/{set}/flashcards', [FlashcardController::class, 'indexBySet']);
    Route::post('sets/{set}/flashcards', [FlashcardController::class, 'storeInSet']);
    Route::delete('flashcards/{flashcard}/image', [FlashcardController::class, 'deleteImage']);
    Route::delete('flashcards/{flashcard}', [FlashcardController::class, 'destroy']);
    Route::put('flashcards/{flashcard}/image', [FlashcardController::class, 'updateImage']);
    Route::put('option/{option}', [FlashcardOptionController::class, 'update']);
    Route::post('flashcards/{flashcard}/option', [FlashcardOptionController::class, 'store']);

    Route::resource('{set}/flashcards', FlashcardController::class);
    Route::put('flashcards/{flashcard}/learned', [FlashcardController::class, 'updateLearned']);

    Route::post('{set}/flashcards/import', [FlashcardImportController::class, 'store']);
    Route::resource('folders', FolderController::class);

    Route::prefix('exams')->group(function () {
        Route::post('/start', [ExamController::class, 'start']);
        Route::get('/{uuid}', [ExamController::class, 'show']);
        Route::post('/{uuid}/answer/{order}', [ExamController::class, 'answer']);
        Route::post('/{uuid}/mark_correct/{answerId}', [ExamController::class, 'markCorrect']);
    });

    Route::get('/exams', [ExamController::class, 'index']);

    Route::prefix('smart-learn')->group(function () {
        Route::post('/start', [SmartLearnController::class, 'start']);
        Route::get('/{uuid}', [SmartLearnController::class, 'show']);
        Route::post('/{uuid}/answer/{order}', [SmartLearnController::class, 'answer']);
    });

    Route::get('/settings', [SettingsController::class, 'show']);
    Route::put('/settings', [SettingsController::class, 'update']);

});