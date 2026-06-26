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
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\NewPasswordController;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\PasswordResetLinkController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentSeminarController;
use App\Http\Controllers\PaymentStatusController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SeminarController;
use App\Http\Controllers\VerifyEmailController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/debug-sentry', function () {
    throw new Exception('My first Sentry error!');
});

Route::get('', fn () => 'ok! @iitc');

Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');
Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [LogoutController::class, 'store']);
});

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::post('competitions/categories', [CategoryController::class, 'store']);

    Route::prefix('competitions/categories/{categoryId}')->group(function () {
        Route::put('', [CategoryController::class, 'update']);
        Route::delete('', [CategoryController::class, 'destroy']);
    });

    Route::get('users', [UserController::class, 'index']);
    Route::get('users/participants', [UserController::class, 'show']);
    Route::delete('users/{userId}', [UserController::class, 'destroy']);

    Route::prefix('competitions/{slug}')->group(function () {
        // using method put, request body not working
        Route::post('', [CompetitionController::class, 'update']);
        Route::delete('', [CompetitionController::class, 'destroy']);
    });
    Route::post('competitions', [CompetitionController::class, 'store']);
    Route::get('seminar', [SeminarController::class, 'index']);
    Route::get('seminar/{userId}', [SeminarController::class, 'tampil']);
    Route::get('seminar/{userId}/admin', [SeminarController::class, 'show']);
    Route::post('seminar/{userId}/update', [SeminarController::class, 'update']);
    Route::get('teams', [TeamController::class, 'index']);
    Route::post('teams/{competitionSlug}', [TeamController::class, 'store']);
    Route::get('teams/{teamId}', [TeamController::class, 'show']);
    Route::get('teams/{teamId}/admin', [AdminGetDetailTeamController::class, 'show']);
    Route::post('teams/{teamId}/update', [TeamController::class, 'update']);
    Route::delete('teams/{teamId}', [TeamController::class, 'destroy']);
    Route::put('teams/join', [JoinTeamController::class, 'store']);
    Route::delete('teams/{teamId}/members/{memberId}', DeleteTeamMemberController::class);
    Route::post('individual/{competitionSlug}', JoinIndividualCompetitionController::class);
    Route::get('profile', [ParticipantController::class, 'show']);
    Route::post('profile', [ParticipantController::class, 'update']);
    Route::get('competitions/mine', CompetitionMineController::class);
    Route::post('payment/{teamId}', [PaymentController::class, 'store']);
    Route::post('paymentseminar/{userId}', [PaymentSeminarController::class, 'store']);
    Route::post('payment/{teamId}/payment-status', [PaymentStatusController::class, 'update']);

    Route::prefix('events')->group(function () {
        Route::get('/', [EventController::class, 'index']);
        Route::post('/', [EventController::class, 'store']);
        Route::put('/{eventId}', [EventController::class, 'update']);
        Route::delete('/{eventId}', [EventController::class, 'destroy']);
        Route::put('/{eventId}/set-active', [EventController::class, 'changeIsActive']);
    });

    // Admin
    Route::prefix('admin')->group(function () {
        Route::get('/teams', [AdminTeamController::class, 'index']);
        Route::get('/teams/{teamId}', [AdminTeamController::class, 'show']);
    });
});

Route::get('competitions/categories', [CategoryController::class, 'index']);
Route::get('competitions', [CompetitionController::class, 'index']);
Route::get('competitions/{slug}', [CompetitionController::class, 'show']);

Route::post('login', [LoginController::class, 'store'])->name('login');
Route::post('register', [RegisterController::class, 'store'])->name('register');
Route::post('forgot-password', [PasswordResetLinkController::class, 'store']);
Route::post('reset-password', [NewPasswordController::class, 'store']);
