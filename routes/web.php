<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\FeatureUsageController;
use App\Http\Controllers\Learning\ChatMessageController;
use App\Http\Controllers\Learning\ChatThreadController;
use App\Http\Controllers\Learning\FlashcardController;
use App\Http\Controllers\Learning\MaterialController;
use App\Http\Controllers\Learning\QuizController;
use App\Http\Controllers\Learning\SummaryController;
use App\Http\Controllers\ProfileController;
use App\Models\AiSummary;
use App\Models\ChatThread;
use App\Models\Material;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard', [
        'materialCount' => Material::count(),
        'summaryCount' => AiSummary::count(),
        'threadCount' => ChatThread::count(),
        'recentMaterials' => Material::with('summaries')->latest()->take(5)->get(),
        'recentThreads' => ChatThread::with('material')->withCount('messages')->latest()->take(5)->get(),
    ]);
})->name('dashboard');

Route::get('/materials', [MaterialController::class, 'index'])->name('materials.index');
Route::get('/upload', [MaterialController::class, 'create'])->name('feature.upload');
Route::post('/materials', [MaterialController::class, 'store'])->name('materials.store');
Route::get('/materials/{material}', [MaterialController::class, 'show'])->name('materials.show');

Route::get('/summary', [SummaryController::class, 'index'])->name('feature.summary');
Route::get('/summaries/{summary}', [SummaryController::class, 'show'])->name('summaries.show');

Route::get('/chat', [ChatThreadController::class, 'index'])->name('feature.chat');
Route::post('/chat', [ChatThreadController::class, 'store'])->name('chat.store');
Route::get('/chat/{chatThread}', [ChatThreadController::class, 'show'])->name('chat.show');
Route::post('/chat/{chatThread}/messages', [ChatMessageController::class, 'store'])->name('chat.messages.store');

Route::get('/quiz', [QuizController::class, 'index'])->name('feature.quiz');
Route::post('/quiz/generate', [QuizController::class, 'generate'])->name('quiz.generate');
Route::post('/quiz/{quizSet}/start', [QuizController::class, 'start'])->name('quiz.start');
Route::post('/quiz/{quizSet}/answer', [QuizController::class, 'answer'])->name('quiz.answer');
Route::post('/quiz/{quizSet}/reset', [QuizController::class, 'reset'])->name('quiz.reset');

Route::get('/flashcards', [FlashcardController::class, 'index'])->name('feature.flashcards');
Route::post('/flashcards/generate', [FlashcardController::class, 'generate'])->name('flashcards.generate');
Route::post('/flashcards/{deck}/review', [FlashcardController::class, 'review'])->name('flashcards.review');

Route::get('/pomodoro', function () {
    return view('mock.pomodoro');
})->name('feature.pomodoro');

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
