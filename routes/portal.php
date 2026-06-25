<?php

use App\Http\Controllers\Web\Portal\ClassController;
use App\Http\Controllers\Web\Portal\LeaveRequestController;
use App\Http\Controllers\Web\Portal\StudentImportController;
use App\Http\Controllers\Web\Portal\TeacherImportController;
use App\Http\Controllers\Web\Portal\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function (): void {
    // Leave Request Routes
    Route::post('/leave-requests', [LeaveRequestController::class, 'store'])->name('leave-requests.store');
    Route::patch('/leave-requests/{id}/approve', [LeaveRequestController::class, 'approve'])->name('leave-requests.approve');
    Route::patch('/leave-requests/{id}/reject', [LeaveRequestController::class, 'reject'])->name('leave-requests.reject');

    // Import Routes
    Route::post('/students/import', [StudentImportController::class, 'store'])->name('students.import');
    Route::post('/teachers/import', [TeacherImportController::class, 'store'])->name('teachers.import');

    // Class Routes
    Route::post('/classes', [ClassController::class, 'store'])->name('classes.store');

    // User Management Routes
    Route::patch('/users/{user}/approve', [UserManagementController::class, 'approve'])->name('users.approve');
    Route::patch('/users/{user}/reject', [UserManagementController::class, 'reject'])->name('users.reject');
    Route::patch('/users/{user}/deactivate', [UserManagementController::class, 'deactivate'])->name('users.deactivate');
});
