<?php

use App\Http\Controllers\Admin\TeamController as AdminTeamController;
use App\Http\Controllers\AdminGetDetailTeamController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CompetitionController;
use App\Http\Controllers\CompetitionMineController;
use App\Http\Controllers\DeleteTeamMemberController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\JoinIndividualCompetitionController;
use App\Http\Controllers\JoinTeamController;
use App\Http\Controllers\LeaveTeamController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\NewPasswordController;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\PasswordResetLinkController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentStatusController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\SeminarController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// ============================================================
// PUBLIC ROUTES (No Authentication Required)
// ============================================================

Route::get('/', fn () => 'ok! @iitc');

// Route::get('/debug-sentry', function () {
//     throw new Exception('My first Sentry error!');
// });

Route::post('/login', [LoginController::class, 'store'])->name('login');
Route::post('/register', [RegisterController::class, 'store'])->name('register');
Route::post('/forgot-password', [PasswordResetLinkController::class, 'store']);
Route::post('/reset-password', [NewPasswordController::class, 'store']);

// Competitions (public read)
// NOTE: 'competitions/mine' must be declared BEFORE '{slug}' to avoid wildcard conflict
Route::get('/competitions', [CompetitionController::class, 'index']);
Route::get('/competitions/categories', [CategoryController::class, 'index']);
Route::get('/competitions/mine', CompetitionMineController::class)->middleware('auth:sanctum');
Route::get('/competitions/{slug}', [CompetitionController::class, 'show']);

// ============================================================
// AUTHENTICATED ROUTES (auth:sanctum)
// ============================================================

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [LogoutController::class, 'store']);


    // ----------------------------------------------------------
    // ADMIN ROUTES
    // ----------------------------------------------------------

    // Categories
    Route::post('/competitions/categories', [CategoryController::class, 'store']);
    Route::prefix('/competitions/categories/{categoryId}')->group(function () {
        Route::put('/', [CategoryController::class, 'update']);
        Route::delete('/', [CategoryController::class, 'destroy']);
    });

    // Competitions
    Route::post('/competitions', [CompetitionController::class, 'store']);
    Route::prefix('/competitions/{slug}')->group(function () {
        Route::post('/', [CompetitionController::class, 'update']); // PUT not used: request body not working with PUT
        Route::delete('/', [CompetitionController::class, 'destroy']);
    });

    // Events
    Route::prefix('/events')->group(function () {
        Route::get('/', [EventController::class, 'index']);
        Route::post('/', [EventController::class, 'store']);
        Route::put('/{eventId}', [EventController::class, 'update']);
        Route::delete('/{eventId}', [EventController::class, 'destroy']);
        Route::put('/{eventId}/set-active', [EventController::class, 'changeIsActive']);
    });

    // Users
    Route::get('/users', [UserController::class, 'index']);
    Route::delete('/users/{userId}', [UserController::class, 'destroy']);

    // Admin — Teams & Seminar
    Route::prefix('/admin')->group(function () {
        Route::get('/teams', [AdminTeamController::class, 'index']);
        Route::get('/teams/{teamId}', [AdminTeamController::class, 'show']);
    });
    Route::get('/teams/{teamId}/admin', [AdminGetDetailTeamController::class, 'show']);

    // Admin — Payment
    Route::post('/payment/{teamId}/payment-status', [PaymentStatusController::class, 'update']);

    // ----------------------------------------------------------
    // USER / PARTICIPANT ROUTES
    // ----------------------------------------------------------

    // Profile
    Route::get('/profile', [ParticipantController::class, 'show']);
    Route::post('/profile', [ParticipantController::class, 'update']);
    Route::get('/users/participants', [UserController::class, 'index']); // Point to index() since show() was deleted

    // Competitions
    Route::post('/individual/{competitionSlug}', JoinIndividualCompetitionController::class);

    // Seminar
    Route::get('/seminar', [SeminarController::class, 'index']);
    Route::post('/seminar/register', [SeminarController::class, 'register']);
    Route::get('/seminar/{userId}', [SeminarController::class, 'show']);
    Route::post('/seminar/{userId}/verify-attendance', [SeminarController::class, 'verifyAttendance']);
    Route::get('/seminar/{userId}/certificate', [SeminarController::class, 'downloadCertificate']);

    // Teams
    Route::get('/teams', [TeamController::class, 'index']);
    Route::get('/teams/mine', [TeamController::class, 'show']);
    Route::post('/teams/mine/update', [TeamController::class, 'update']);
    Route::post('/teams/mine/submission', [TeamController::class, 'submit']);
    Route::delete('/teams/mine', [TeamController::class, 'destroy']);
    Route::post('/teams/{competitionSlug}', [TeamController::class, 'store']);
    Route::put('/teams/join', [JoinTeamController::class, 'store']);
    Route::delete('/teams/mine/leave', LeaveTeamController::class);
    Route::delete('/teams/mine/members/{memberId}', DeleteTeamMemberController::class);

    // Payment
    Route::get('/payment/mine/status', [PaymentController::class, 'showStatus']);
    Route::post('/payment/mine', [PaymentController::class, 'store']);
});
