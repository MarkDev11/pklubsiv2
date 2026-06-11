<?php

use App\Http\Controllers\Admin\AkunController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\ExportController;
use App\Http\Controllers\Admin\ImportController;
use App\Http\Controllers\Admin\MahasiswaController;
use App\Http\Controllers\Admin\NilaiController as AdminNilaiController;
use App\Http\Controllers\Admin\OpeningHourController;
use App\Http\Controllers\Admin\SystemLogController;
use App\Http\Controllers\AiAssistantController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\OtpController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\SsoController;
use App\Http\Controllers\Dosen\DashboardController as DosenDashboard;
use App\Http\Controllers\Dosen\DataMahasiswaController as DosenDataMahasiswa;
use App\Http\Controllers\Dosen\NilaiController as DosenNilaiController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\Mahasiswa\DashboardController as MahasiswaDashboard;
use App\Http\Controllers\Mahasiswa\LaporanController;
use App\Http\Controllers\Mahasiswa\ProposalController;
use App\Http\Controllers\Mentor\DashboardController as MentorDashboard;
use App\Http\Controllers\Mentor\DataMahasiswaController as MentorDataMahasiswa;
use App\Http\Controllers\Mentor\NilaiController as MentorNilaiController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route(auth()->user()->role->dashboardRoute());
    }

    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| Auth Routes (Guest)
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->middleware('throttle:5,1');

    Route::get('/admin/login', [AuthenticatedSessionController::class, 'createAdmin'])->name('login.admin');
    Route::post('/admin/login', [AuthenticatedSessionController::class, 'store'])->name('login.admin.store')->middleware('throttle:5,1');

    Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('auth.google');
    Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('auth.google.callback');

    // Internal SSO Endpoint
    Route::get('/sso/portal', [SsoController::class, 'handle'])->name('sso.portal');

    // Forgot Password (OTP)
    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('/forgot-password/verify', [PasswordResetLinkController::class, 'showVerifyForm'])->name('password.verify-otp');
    Route::post('/forgot-password/verify', [PasswordResetLinkController::class, 'verifyOtp'])->name('password.confirm-otp');
    Route::post('/forgot-password/resend', [PasswordResetLinkController::class, 'resendOtp'])->name('password.resend-otp')->middleware('throttle:3,1');

    Route::get('/reset-password', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.update');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    // Logout
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    // OTP Verification
    Route::get('/otp', [OtpController::class, 'show'])->name('otp.show');
    Route::post('/otp/verify', [OtpController::class, 'verify'])->name('otp.verify');
    Route::post('/otp/resend', [OtpController::class, 'resend'])->name('otp.resend');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // AI Assistant - rate limited to 10 requests per minute
    Route::middleware('throttle:10,1')->group(function () {
        Route::post('/ai/chat', [AiAssistantController::class, 'chat'])->name('ai.chat');
    });

    // File Serving - encrypted filename
    Route::get('/files/{encrypted}', [FileController::class, 'serve'])->name('files.serve');

    // Proposal Document Download
    Route::get('/proposal/{id}/download/{type}', [FileController::class, 'downloadProposalDocument'])->name('proposal.download');

    // Export Download (public with short code, rate limited)
    Route::middleware('throttle:20,1')->group(function () {
        Route::get('/exports/file/{export}/download', [ExportController::class, 'downloadFile'])->name('exports.file.download');
        Route::get('/exports/{code}/download', [ExportController::class, 'download'])->name('exports.download');
    });

    /*
    |----------------------------------------------------------------------
    | Admin Routes
    |----------------------------------------------------------------------
    */
    Route::prefix('admin')->middleware('role:admin')->name('admin.')->group(function () {
        Route::get('/', [AdminDashboard::class, 'index'])->name('dashboard');

        Route::get('/akun', [AkunController::class, 'index'])->name('akun.index');
        Route::post('/akun/datatable', [AkunController::class, 'datatable'])->name('akun.datatable');
        Route::get('/akun/create', [AkunController::class, 'create'])->name('akun.create');
        Route::post('/akun', [AkunController::class, 'store'])->name('akun.store');
        Route::get('/akun/{encrypted}/edit', [AkunController::class, 'edit'])->name('akun.edit');
        Route::put('/akun/{encrypted}', [AkunController::class, 'update'])->name('akun.update');
        Route::delete('/akun/{encrypted}', [AkunController::class, 'destroy'])->name('akun.destroy');
        Route::post('/akun/{encrypted}/reset-password', [AkunController::class, 'resetPassword'])->name('akun.reset-password');
        Route::get('/akun/{encrypted}/log', [AkunController::class, 'log'])->name('akun.log');

        Route::get('/import', [ImportController::class, 'index'])->name('import.index');
        Route::post('/import', [ImportController::class, 'import'])->name('import.store');

        Route::get('/tanggal', [OpeningHourController::class, 'index'])->name('tanggal.index');
        Route::put('/tanggal', [OpeningHourController::class, 'update'])->name('tanggal.update');

        Route::get('/nilai/pkl', [AdminNilaiController::class, 'pklIndex'])->name('nilai.pkl');
        Route::get('/nilai/msib', [AdminNilaiController::class, 'msibIndex'])->name('nilai.msib');
        Route::post('/nilai/save', [AdminNilaiController::class, 'saveNilai'])->name('nilai.save');

        // Export routes (with filter & chunked processing)
        Route::get('/exports/history', [ExportController::class, 'history'])->name('exports.history');
        Route::delete('/exports/cleanup', [ExportController::class, 'cleanup'])->name('exports.cleanup');
        Route::delete('/exports/cleanup-all', [ExportController::class, 'cleanupAll'])->name('exports.cleanup-all');
        Route::get('/exports/pkl/config', [ExportController::class, 'pklConfig'])->name('exports.pkl.config');
        Route::get('/exports/msib/config', [ExportController::class, 'msibConfig'])->name('exports.msib.config');
        Route::post('/exports/count', [ExportController::class, 'count'])->name('exports.count');
        Route::post('/exports/initiate', [ExportController::class, 'initiate'])->name('exports.initiate');
        Route::get('/exports/{export}/progress', [ExportController::class, 'progress'])->name('exports.progress');
        Route::post('/exports/{export}/chunk', [ExportController::class, 'processChunk'])->name('exports.chunk');
        Route::post('/exports/{export}/finalize', [ExportController::class, 'finalize'])->name('exports.finalize');
        Route::get('/exports/{export}/status', [ExportController::class, 'status'])->name('exports.status');

        Route::get('/mahasiswa', [MahasiswaController::class, 'index'])->name('mahasiswa.index');
        Route::post('/mahasiswa/datatable', [MahasiswaController::class, 'datatable'])->name('mahasiswa.datatable');

        Route::get('/logs/error', [SystemLogController::class, 'index'])->name('logs.error');
        Route::delete('/logs/error/clear', [SystemLogController::class, 'clear'])->name('logs.error.clear');
        Route::delete('/logs/error/{id}', [SystemLogController::class, 'destroy'])->name('logs.error.destroy');
    });

    /*
    |----------------------------------------------------------------------
    | Mahasiswa Routes
    |----------------------------------------------------------------------
    */
    Route::prefix('mahasiswa')->middleware('role:mahasiswa')->name('mahasiswa.')->group(function () {
        Route::get('/', [MahasiswaDashboard::class, 'index'])->name('dashboard');

        Route::get('/proposal', [ProposalController::class, 'index'])->name('proposal.index');
        Route::post('/proposal', [ProposalController::class, 'store'])->name('proposal.store');
        Route::put('/proposal', [ProposalController::class, 'update'])->name('proposal.update');

        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::post('/laporan', [LaporanController::class, 'upload'])->name('laporan.upload');
        Route::post('/laporan/reset', [LaporanController::class, 'reset'])->name('laporan.reset');
    });

    /*
    |----------------------------------------------------------------------
    | Dosen Routes
    |----------------------------------------------------------------------
    */
    Route::prefix('dosen')->middleware('role:dosen')->name('dosen.')->group(function () {
        Route::get('/', [DosenDashboard::class, 'index'])->name('dashboard');

        Route::get('/mahasiswa/pkl', [DosenDataMahasiswa::class, 'pklIndex'])->name('mahasiswa.pkl');
        Route::get('/mahasiswa/msib', [DosenDataMahasiswa::class, 'msibIndex'])->name('mahasiswa.msib');
        Route::get('/mahasiswa/{encrypted}/detail', [DosenDataMahasiswa::class, 'detail'])->name('mahasiswa.detail');

        Route::get('/nilai/pkl', [DosenNilaiController::class, 'pklIndex'])->name('nilai.pkl');
        Route::get('/nilai/msib', [DosenNilaiController::class, 'msibIndex'])->name('nilai.msib');
        Route::post('/nilai/save', [DosenNilaiController::class, 'saveNilai'])->middleware('throttle:60,1')->name('nilai.save');

        Route::get('/pdf/pkl', [DosenNilaiController::class, 'generatePdfPkl'])->name('pdf.pkl');
        Route::get('/pdf/msib', [DosenNilaiController::class, 'generatePdfMsib'])->name('pdf.msib');
        Route::get('/exports/pkl', [DosenNilaiController::class, 'exportPklIndex'])->name('exports.pkl.index');
        Route::get('/exports/msib', [DosenNilaiController::class, 'exportMsibIndex'])->name('exports.msib.index');
        Route::post('/exports/pkl', [DosenNilaiController::class, 'exportPkl'])->middleware('throttle:5,1')->name('exports.pkl');
        Route::post('/exports/msib', [DosenNilaiController::class, 'exportMsib'])->middleware('throttle:5,1')->name('exports.msib');
    });

    /*
    |----------------------------------------------------------------------
    | Mentor Routes
    |----------------------------------------------------------------------
    */
    Route::prefix('mentor')->middleware('role:mentor')->name('mentor.')->group(function () {
        Route::get('/', [MentorDashboard::class, 'index'])->name('dashboard');

        Route::get('/mahasiswa', [MentorDataMahasiswa::class, 'index'])->name('mahasiswa.index');
        Route::redirect('/mahasiswa/pkl', '/mentor/mahasiswa')->name('mahasiswa.pkl');
        Route::redirect('/mahasiswa/msib', '/mentor/mahasiswa')->name('mahasiswa.msib');
        Route::get('/mahasiswa/{encrypted}/detail', [MentorDataMahasiswa::class, 'detail'])->name('mahasiswa.detail');

        Route::get('/nilai', [MentorNilaiController::class, 'index'])->name('nilai.index');
        Route::redirect('/nilai/pkl', '/mentor/nilai')->name('nilai.pkl');
        Route::redirect('/nilai/msib', '/mentor/nilai')->name('nilai.msib');
        Route::post('/nilai/save', [MentorNilaiController::class, 'saveNilai'])->middleware('throttle:60,1')->name('nilai.save');

        Route::get('/pdf/pkl', [MentorNilaiController::class, 'generatePdfPkl'])->name('pdf.pkl');
        Route::get('/pdf/msib', [MentorNilaiController::class, 'generatePdfMsib'])->name('pdf.msib');
    });
});
