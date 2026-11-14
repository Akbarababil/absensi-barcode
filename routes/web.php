<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentImportController;
use App\Http\Controllers\AttendanceController;

// LOGIN
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.process');
});

// SEMUA PENGGUNA YANG SUDAH LOGIN
Route::middleware('auth')->group(function () {

    // KHUSUS ADMIN
    Route::middleware('role:admin')->group(function () {

        // DASHBOARD
        Route::get('/dashboard', function () {
            return view('dashboard');
        })->name('dashboard');

        // DATA KELAS
        Route::resource('classrooms', ClassroomController::class);

        // IMPORT DATA SISWA
        Route::get('/students/import', [StudentImportController::class, 'create'])
            ->name('students.import.create');

        Route::post('/students/import', [StudentImportController::class, 'store'])
            ->name('students.import.store');

        // DATA SISWA
        Route::resource('students', StudentController::class);

        // CETAK KARTU SISWA
        Route::get('/students/{student}/card', [StudentController::class, 'card'])
            ->name('students.card');

        // HALAMAN DATA ABSENSI ADMIN
        Route::get('/attendance', [AttendanceController::class, 'index'])
            ->name('attendance.index');
    });

    // KHUSUS PETUGAS
    Route::middleware('role:petugas')->group(function () {

        // HALAMAN SCAN BARCODE
        Route::get('/petugas/attendance', [AttendanceController::class, 'petugas'])
            ->name('petugas.attendance');

        // PROSES SCAN BARCODE
        Route::post('/attendance/scan', [AttendanceController::class, 'scan'])
            ->name('attendance.scan');
    });

    // LOGOUT ADMIN DAN PETUGAS
    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');
});

// HALAMAN UTAMA
Route::get('/', function () {
    return redirect('/login');
});
