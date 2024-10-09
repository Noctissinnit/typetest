<?php

use App\Http\Controllers\TypingTestController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/typing-test', [TypingTestController::class, 'index'])->name('typingTest.index');
Route::post('/calculate-result', [TypingTestController::class, 'calculateResult'])->name('calculate.result');
Route::get('/leaderboard', [TypingTestController::class, 'leaderboard'])->name('leaderboard.index');
Route::get('/random-text', [TypingTestController::class, 'randomText'])->name('typingTest.randomText');
