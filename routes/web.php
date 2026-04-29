<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

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

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Student routes
Route::post('/students', [DashboardController::class, 'storeStudent'])->name('students.store');
Route::delete('/students/{id}', [DashboardController::class, 'deleteStudent'])->name('students.delete');
Route::post('/students/upload', [DashboardController::class, 'uploadStudents'])->name('students.upload');

// Attendance routes
Route::post('/attendance', [DashboardController::class, 'storeAttendance'])->name('attendance.store');
Route::delete('/attendance/{id}', [DashboardController::class, 'deleteAttendance'])->name('attendance.delete');

// Presence kiosk flow for RFID + face recognition
Route::get('/kiosk', [DashboardController::class, 'showKiosk'])->name('kiosk');
Route::post('/kiosk/card-scan', [DashboardController::class, 'handleCardScan'])->name('kiosk.card-scan');
Route::get('/kiosk/face/{student}', [DashboardController::class, 'showFaceRecognition'])->name('kiosk.face');
Route::post('/kiosk/face-confirm', [DashboardController::class, 'confirmFaceRecognition'])->name('kiosk.face-confirm');

