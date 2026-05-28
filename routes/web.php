<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Web\PortalController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::get('/dashboard', [PortalController::class, 'index'])->name('dashboard');
    Route::redirect('/students', '/dashboard');
    Route::redirect('/teachers', '/dashboard');
    Route::redirect('/attendance', '/dashboard');
    Route::redirect('/leave-requests', '/dashboard');
    Route::redirect('/rfid-registration', '/dashboard');
    Route::redirect('/face-profiles', '/dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
