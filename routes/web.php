<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\FeatureUsageController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::get('/upload', function () {
    return view('mock.upload');
})->name('feature.upload');

Route::get('/summary', function () {
    return view('mock.summary');
})->name('feature.summary');

Route::get('/chat', function () {
    return view('mock.chat');
})->name('feature.chat');

Route::get('/quiz', function () {
    return view('mock.quiz');
})->name('feature.quiz');

Route::get('/flashcards', function () {
    return view('mock.flashcards');
})->name('feature.flashcards');

Route::get('/pomodoro', function () {
    return view('mock.pomodoro');
})->name('feature.pomodoro');

// Route::get('/feynman', function () {
//     return view('mock.feynman');
// })->name('feature.feynman');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    Route::post('/track-feature', [FeatureUsageController::class, 'track'])->name('feature.track');
});

Route::middleware(['auth', \App\Http\Middleware\AdminMiddleware::class])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
});

require __DIR__.'/auth.php';
