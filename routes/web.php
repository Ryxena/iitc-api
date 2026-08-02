<?php

use App\Http\Controllers\Admin\AdminCompetitionController;
use App\Http\Controllers\Admin\AdminMediaPartnerController;
use App\Http\Controllers\Admin\AdminSeminarManagementController;
use App\Http\Controllers\Admin\AdminParticipantRecapController;
use App\Http\Controllers\Admin\AdminSponsorController;
use App\Http\Controllers\Admin\AdminTeamRecapController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ExportController as AdminExportController;
use App\Http\Controllers\Admin\PaymentAdminController;
use App\Http\Controllers\Admin\SeminarAdminController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login-web');
});

Route::get('/dashboard', function () {
    return redirect()->route('admin.dashboard');
})
    ->middleware(['auth', 'verified'])
    ->name('dashboard');




// ============================================================
// ADMIN WEB ROUTES
// ============================================================
Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Payment validation (Admin + Super Admin)
    Route::get('/payments', [PaymentAdminController::class, 'index'])->name('payments.index');
    Route::get('/payments/{teamId}', [PaymentAdminController::class, 'show'])->name('payments.show');
    Route::patch('/payments/{teamId}', [PaymentAdminController::class, 'update'])->name('payments.update');

    // Participant Competition Recap (Admin + Super Admin)
    Route::get('/teams-recap', [AdminTeamRecapController::class, 'index'])->name('teams.recap');
    Route::get('/teams-recap/export', [AdminTeamRecapController::class, 'export'])->name('teams.recap.export');
    Route::get('/teams-recap/{id}', [AdminTeamRecapController::class, 'show'])->name('teams.recap.show');
    
    // Individual Recap
    Route::get('/participants-recap', [AdminParticipantRecapController::class, 'index'])->name('participants.recap');
    Route::get('/participants-recap/{id}', [AdminParticipantRecapController::class, 'show'])->name('participants.recap.show');

    // Export teams (Admin + Super Admin)
    Route::get('/export/teams', [AdminExportController::class, 'teams'])->name('export.teams');

    // ── Super Admin only ──────────────────────────────────────────
    Route::middleware('super-admin')->group(function () {

        // Users
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('/users/{userId}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
        Route::patch('/users/{userId}', [AdminUserController::class, 'update'])->name('users.update');
        Route::delete('/users/{userId}', [AdminUserController::class, 'destroy'])->name('users.destroy');

        // Competitions
        Route::get('/competitions', [AdminCompetitionController::class, 'index'])->name('competitions.index');
        Route::post('/competitions', [AdminCompetitionController::class, 'store'])->name('competitions.store');
        Route::patch('/competitions/{slug}', [AdminCompetitionController::class, 'update'])->name('competitions.update');
        Route::delete('/competitions/{slug}', [AdminCompetitionController::class, 'destroy'])->name('competitions.destroy');

        // Seminar CRUD management
        Route::get('/seminars', [AdminSeminarManagementController::class, 'index'])->name('seminars.index');
        Route::post('/seminars', [AdminSeminarManagementController::class, 'store'])->name('seminars.store');
        Route::patch('/seminars/{seminar}', [AdminSeminarManagementController::class, 'update'])->name('seminars.update');
        Route::delete('/seminars/{seminar}', [AdminSeminarManagementController::class, 'destroy'])->name('seminars.destroy');
        Route::patch('/seminars/{seminar}/toggle-active', [AdminSeminarManagementController::class, 'toggleActive'])->name('seminars.toggle-active');

        // Seminar participant management & verify
        Route::get('/seminar', [SeminarAdminController::class, 'index'])->name('seminar.index');
        Route::post('/seminar/{userId}/verify', [SeminarAdminController::class, 'verify'])->name('seminar.verify');
        Route::post('/seminar/bulk-verify', [SeminarAdminController::class, 'bulkVerify'])->name('seminar.bulk-verify');

        // Export seminars
        Route::get('/export/seminars', [AdminExportController::class, 'seminars'])->name('export.seminars');

        // Media Partners
        Route::get('/media-partners', [AdminMediaPartnerController::class, 'index'])->name('media-partners.index');
        Route::post('/media-partners', [AdminMediaPartnerController::class, 'store'])->name('media-partners.store');
        Route::post('/media-partners/{mediaPartner}', [AdminMediaPartnerController::class, 'update'])->name('media-partners.update');
        Route::delete('/media-partners/{mediaPartner}', [AdminMediaPartnerController::class, 'destroy'])->name('media-partners.destroy');

        // Sponsors
        Route::get('/sponsors', [AdminSponsorController::class, 'index'])->name('sponsors.index');
        Route::post('/sponsors', [AdminSponsorController::class, 'store'])->name('sponsors.store');
        Route::post('/sponsors/{sponsor}', [AdminSponsorController::class, 'update'])->name('sponsors.update');
        Route::delete('/sponsors/{sponsor}', [AdminSponsorController::class, 'destroy'])->name('sponsors.destroy');
    });
});

require __DIR__ . '/auth.php';
