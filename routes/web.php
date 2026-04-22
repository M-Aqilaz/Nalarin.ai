<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminDocumentController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\FeatureUsageController;
use App\Http\Controllers\Learning\ChatMessageController;
use App\Http\Controllers\Learning\ChatThreadController;
use App\Http\Controllers\Learning\FlashcardController;
use App\Http\Controllers\Learning\MaterialController;
use App\Http\Controllers\Learning\QuizController;
use App\Http\Controllers\Learning\SummaryController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PricingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudyMatchingController;
use App\Http\Controllers\StudyRoomController;
use App\Http\Controllers\StudyRoomMessageController;
use App\Models\AiSummary;
use App\Models\ChatThread;
use App\Models\Material;
use App\Models\StudyMatch;
use App\Models\StudyRoom;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pages.public.welcome');
});

Route::get('/pricing', PricingController::class)->name('pricing');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();

        return view('pages.user.dashboard', [
            'materialCount' => Material::where('user_id', $user->id)->count(),
            'summaryCount' => AiSummary::where('user_id', $user->id)->count(),
            'threadCount' => ChatThread::where('user_id', $user->id)->count(),
            'roomCount' => $user->roomMemberships()->where('status', 'active')->count(),
            'activeMatchCount' => StudyMatch::where('status', 'active')
                ->where(fn ($query) => $query->where('user_one_id', $user->id)->orWhere('user_two_id', $user->id))
                ->count(),
            'recentMaterials' => Material::where('user_id', $user->id)->with('summaries')->latest()->take(5)->get(),
            'recentThreads' => ChatThread::where('user_id', $user->id)->with('material')->withCount('messages')->latest()->take(5)->get(),
            'recentRooms' => StudyRoom::whereHas('members', fn ($query) => $query->where('user_id', $user->id)->where('status', 'active'))
                ->withCount('members')
                ->latest()
                ->take(5)
                ->get(),
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
    Route::get('/chat/{chatThread}/messages', [ChatMessageController::class, 'index'])->name('chat.messages.index');
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
        return view('pages.user.pomodoro');
    })->name('feature.pomodoro');

    Route::get('/rooms', [StudyRoomController::class, 'index'])->name('rooms.index');
    Route::post('/rooms', [StudyRoomController::class, 'store'])->middleware('room.limit')->name('rooms.store');
    Route::get('/rooms/{room}', [StudyRoomController::class, 'show'])->name('rooms.show');
    Route::post('/rooms/{room}/join', [StudyRoomController::class, 'join'])->name('rooms.join');
    Route::post('/rooms/{room}/leave', [StudyRoomController::class, 'leave'])->name('rooms.leave');
    Route::get('/rooms/{room}/messages', [StudyRoomMessageController::class, 'index'])->name('rooms.messages.index');
    Route::post('/rooms/{room}/typing', [StudyRoomMessageController::class, 'typing'])->name('rooms.typing');
    Route::post('/rooms/{room}/messages', [StudyRoomMessageController::class, 'store'])->name('rooms.messages.store');

    Route::get('/matchmaking', [StudyMatchingController::class, 'index'])->name('matchmaking.index');
    Route::post('/matchmaking/profile', [StudyMatchingController::class, 'updateProfile'])->name('matchmaking.profile.update');
    Route::post('/matchmaking/search', [StudyMatchingController::class, 'search'])->name('matchmaking.search');
    Route::post('/matchmaking/cancel', [StudyMatchingController::class, 'cancel'])->name('matchmaking.cancel');
    Route::get('/matches/{match}', [StudyMatchingController::class, 'show'])->name('matches.show');
    Route::get('/matches/{match}/messages', [StudyMatchingController::class, 'messages'])->name('matches.messages.index');
    Route::post('/matches/{match}/typing', [StudyMatchingController::class, 'typing'])->name('matches.typing');
    Route::post('/matches/{match}/messages', [StudyMatchingController::class, 'sendMessage'])->name('matches.messages.store');
    Route::post('/matches/{match}/end', [StudyMatchingController::class, 'end'])->name('matches.end');
    Route::post('/matches/{match}/block', [StudyMatchingController::class, 'block'])->name('matches.block');
    Route::post('/matches/{match}/report', [StudyMatchingController::class, 'report'])->name('matches.report');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');

    Route::post('/track-feature', [FeatureUsageController::class, 'track'])->name('feature.track');
});

Route::middleware(['auth', \App\Http\Middleware\AdminMiddleware::class])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/monitoring-ai', [AdminController::class, 'monitoringAi'])->name('admin.monitoring-ai');
    Route::get('/admin/statistik-pembelajaran', [AdminController::class, 'statistikPembelajaran'])->name('admin.statistik-pembelajaran');
    Route::get('/admin/users', [AdminUserController::class, 'index'])->name('admin.users.index');
    Route::patch('/admin/users/{user}/suspend', [AdminUserController::class, 'suspend'])->name('admin.users.suspend');
    Route::patch('/admin/users/{user}/activate', [AdminUserController::class, 'activate'])->name('admin.users.activate');
    Route::get('/admin/documents', [AdminDocumentController::class, 'index'])->name('admin.documents.index');
    Route::delete('/admin/documents/{material}', [AdminDocumentController::class, 'destroy'])->name('admin.documents.destroy');
});

require __DIR__.'/auth.php';
