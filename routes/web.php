<?php

use App\Http\Controllers\Admin\AkunController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\ImportController;
use App\Http\Controllers\Admin\MahasiswaController;
use App\Http\Controllers\Admin\NilaiController as AdminNilaiController;
use App\Http\Controllers\Admin\OpeningHourController;
use App\Http\Controllers\Admin\PdfController as AdminPdfController;
use App\Http\Controllers\Admin\SystemLogController;
use App\Http\Controllers\AiAssistantController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\OtpController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
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
    Route::get('/sso/portal', [\App\Http\Controllers\Auth\SsoController::class, 'handle'])->name('sso.portal');

    // Forgot Password (OTP)
    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('/forgot-password/verify', [PasswordResetLinkController::class, 'showVerifyForm'])->name('password.verify-otp');
    Route::post('/forgot-password/verify', [PasswordResetLinkController::class, 'verifyOtp'])->name('password.confirm-otp');
    
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

    // File Serving
    Route::get('/files/{filename}', [FileController::class, 'serve'])->name('files.serve');

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

        Route::get('/pdf/pkl', [AdminPdfController::class, 'pklPdf'])->name('pdf.pkl');
        Route::get('/pdf/msib', [AdminPdfController::class, 'msibPdf'])->name('pdf.msib');

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

        Route::get('/nilai/pkl', [DosenNilaiController::class, 'pklIndex'])->name('nilai.pkl');
        Route::get('/nilai/msib', [DosenNilaiController::class, 'msibIndex'])->name('nilai.msib');
        Route::post('/nilai/save', [DosenNilaiController::class, 'saveNilai'])->name('nilai.save');

        Route::get('/pdf/pkl', [DosenNilaiController::class, 'generatePdfPkl'])->name('pdf.pkl');
        Route::get('/pdf/msib', [DosenNilaiController::class, 'generatePdfMsib'])->name('pdf.msib');
    });

    /*
    |----------------------------------------------------------------------
    | Mentor Routes
    |----------------------------------------------------------------------
    */
    Route::prefix('mentor')->middleware('role:mentor')->name('mentor.')->group(function () {
        Route::get('/', [MentorDashboard::class, 'index'])->name('dashboard');

        Route::get('/mahasiswa/pkl', [MentorDataMahasiswa::class, 'pklIndex'])->name('mahasiswa.pkl');
        Route::get('/mahasiswa/msib', [MentorDataMahasiswa::class, 'msibIndex'])->name('mahasiswa.msib');

        Route::get('/nilai/pkl', [MentorNilaiController::class, 'pklIndex'])->name('nilai.pkl');
        Route::get('/nilai/msib', [MentorNilaiController::class, 'msibIndex'])->name('nilai.msib');
        Route::post('/nilai/save', [MentorNilaiController::class, 'saveNilai'])->name('nilai.save');

        Route::get('/pdf/pkl', [MentorNilaiController::class, 'generatePdfPkl'])->name('pdf.pkl');
        Route::get('/pdf/msib', [MentorNilaiController::class, 'generatePdfMsib'])->name('pdf.msib');
    });
});
