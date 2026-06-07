<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Web\PortalController;
use App\Http\Controllers\Web\AttendanceController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

// Public Attendance Routes (Kiosk)
Route::group(['prefix' => 'attendance', 'middleware' => ['throttle:60,1']], function () {
    Route::get('/rfid-check-in', [AttendanceController::class, 'rfidCheckIn'])->name('attendance.rfid');
    Route::post('/rfid-verify', [AttendanceController::class, 'rfidVerify'])->name('attendance.rfid-verify');

    Route::get('/face-recognition', [AttendanceController::class, 'faceRecognition'])->name('attendance.face');
    Route::post('/face-verify', [AttendanceController::class, 'faceVerify'])->name('attendance.face-verify');
});

Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::get('/dashboard', [PortalController::class, 'index'])->name('dashboard');
    Route::redirect('/students', '/dashboard');
    Route::redirect('/teachers', '/dashboard');
    Route::redirect('/leave-requests', '/dashboard');
    Route::redirect('/rfid-registration', '/dashboard');
    Route::redirect('/face-profiles', '/dashboard');

    // Portal API routes
    require __DIR__.'/portal.php';
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
