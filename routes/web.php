<?php

use App\Http\Controllers\Admin\AdminCompetitionController;
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

    // Export teams (Admin + Super Admin)
    Route::get('/export/teams', [AdminExportController::class, 'teams'])->name('export.teams');

    // ── Super Admin only ──────────────────────────────────────────
    Route::middleware('super-admin')->group(function () {

        // Users
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::delete('/users/{userId}', [AdminUserController::class, 'destroy'])->name('users.destroy');

        // Competitions
        Route::get('/competitions', [AdminCompetitionController::class, 'index'])->name('competitions.index');
        Route::post('/competitions', [AdminCompetitionController::class, 'store'])->name('competitions.store');
        Route::patch('/competitions/{slug}', [AdminCompetitionController::class, 'update'])->name('competitions.update');
        Route::delete('/competitions/{slug}', [AdminCompetitionController::class, 'destroy'])->name('competitions.destroy');

        // Seminar management
        Route::get('/seminar', [SeminarAdminController::class, 'index'])->name('seminar.index');
        Route::post('/seminar/{userId}/verify', [SeminarAdminController::class, 'verify'])->name('seminar.verify');
        Route::post('/seminar/bulk-verify', [SeminarAdminController::class, 'bulkVerify'])->name('seminar.bulk-verify');

        // Export seminars
        Route::get('/export/seminars', [AdminExportController::class, 'seminars'])->name('export.seminars');
    });
});

require __DIR__ . '/auth.php';
