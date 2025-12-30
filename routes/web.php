<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

Route::get('/create', function () {
    return view('flashcards.create');
});

Route::get('/learn', function () {
    return view('flashcards.learn');
});

Route::get('/sets', function () {
    return view('flashcards.sets');
});


Route::get('/smart-learn', function () {
    return view('smart-learn.start');
});

Route::get('/smart-learn/{uuid}', function () {
    return view('smart-learn.session');
});

Route::get('/exam', fn () => view('exam.start'));
Route::get('/exam/{uuid}', fn () => view('exam.session'));
Route::get('/exam/{uuid}/result', fn () => view('exam.result'));
Route::get('/exams', fn () => view('exam.history'));
Route::get('/settings', fn () => view('settings'));
