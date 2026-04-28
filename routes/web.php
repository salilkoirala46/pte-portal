<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\StudentManagementController;
use App\Http\Controllers\Admin\SubscriptionPlanController;
use App\Http\Controllers\Admin\TenantController;
use App\Http\Controllers\Student\DashboardController;
use App\Http\Controllers\Student\ListeningController;
use App\Http\Controllers\Student\ReadingController;
use App\Http\Controllers\Student\SpeakingController;
use App\Http\Controllers\Student\SubscriptionController;
use App\Http\Controllers\Student\WritingController;
use App\Http\Controllers\Student\ResultController;
use Illuminate\Support\Facades\Route;

// ─── Auth ─────────────────────────────────────────────────────────────────────
Route::get('/', [AuthController::class, 'showLanding'])->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login',      [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',     [AuthController::class, 'login'])->name('login.post');
    Route::get('/register',   [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register',  [AuthController::class, 'register'])->name('register.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ─── Student Area ──────────────────────────────────────────────────────────────
Route::middleware(['auth', 'tenant'])->prefix('student')->name('student.')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Subscription
    Route::get('/subscription',        [SubscriptionController::class, 'index'])->name('subscription.index');
    Route::post('/subscription/{plan}', [SubscriptionController::class, 'subscribe'])->name('subscription.subscribe');
    Route::delete('/subscription',     [SubscriptionController::class, 'cancel'])->name('subscription.cancel');

    // Exam modules (require active subscription)
    Route::middleware('subscribed')->group(function () {

        // Speaking
        Route::prefix('speaking')->name('speaking.')->group(function () {
            Route::get('/',                     [SpeakingController::class, 'index'])->name('index');
            Route::get('/practice/{type}',      [SpeakingController::class, 'practice'])->name('practice');
            Route::get('/question/{question}',  [SpeakingController::class, 'question'])->name('question');
            Route::post('/submit',              [SpeakingController::class, 'submit'])->name('submit');
            Route::get('/results/{attempt}',    [SpeakingController::class, 'results'])->name('results');
        });

        // Reading
        Route::prefix('reading')->name('reading.')->group(function () {
            Route::get('/',                     [ReadingController::class, 'index'])->name('index');
            Route::get('/practice/{type}',      [ReadingController::class, 'practice'])->name('practice');
            Route::get('/question/{question}',  [ReadingController::class, 'question'])->name('question');
            Route::post('/submit',              [ReadingController::class, 'submit'])->name('submit');
            Route::get('/results/{attempt}',    [ReadingController::class, 'results'])->name('results');
        });

        // Writing
        Route::prefix('writing')->name('writing.')->group(function () {
            Route::get('/',                     [WritingController::class, 'index'])->name('index');
            Route::get('/practice/{type}',      [WritingController::class, 'practice'])->name('practice');
            Route::get('/question/{question}',  [WritingController::class, 'question'])->name('question');
            Route::post('/submit',              [WritingController::class, 'submit'])->name('submit');
            Route::get('/results/{attempt}',    [WritingController::class, 'results'])->name('results');
        });

        // Listening
        Route::prefix('listening')->name('listening.')->group(function () {
            Route::get('/',                     [ListeningController::class, 'index'])->name('index');
            Route::get('/practice/{type}',      [ListeningController::class, 'practice'])->name('practice');
            Route::get('/question/{question}',  [ListeningController::class, 'question'])->name('question');
            Route::post('/submit',              [ListeningController::class, 'submit'])->name('submit');
            Route::get('/results/{attempt}',    [ListeningController::class, 'results'])->name('results');
        });

        // Mock Test
        Route::get('/mock-test',       [DashboardController::class, 'mockTest'])->name('mock-test');
        Route::post('/mock-test/start',[DashboardController::class, 'startMockTest'])->name('mock-test.start');

        // Results
        Route::get('/results',         [ResultController::class, 'index'])->name('results.index');
        Route::get('/results/{attempt}',[ResultController::class, 'show'])->name('results.show');
    });
});

// ─── Admin Area (Tenant Admin) ─────────────────────────────────────────────────
Route::middleware(['auth', 'tenant'])->prefix('admin')->name('admin.')->group(function () {
    Route::middleware('can:admin')->group(function () {
        Route::get('/dashboard',   [AdminDashboard::class, 'index'])->name('dashboard');

        Route::resource('questions',        QuestionController::class);
        Route::resource('students',         StudentManagementController::class);
        Route::resource('plans',            SubscriptionPlanController::class);

        Route::get('/reports',             [AdminDashboard::class, 'reports'])->name('reports');
        Route::get('/settings',            [AdminDashboard::class, 'settings'])->name('settings');
        Route::put('/settings',            [AdminDashboard::class, 'updateSettings'])->name('settings.update');
    });
});

// ─── Super Admin (Central) ─────────────────────────────────────────────────────
Route::middleware(['auth'])->prefix('super-admin')->name('super-admin.')->group(function () {
    Route::middleware('can:super-admin')->group(function () {
        Route::resource('tenants', TenantController::class);
    });
});
