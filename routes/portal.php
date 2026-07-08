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
    Route::post('/classes/{class}/students', [ClassController::class, 'addStudents'])->name('classes.add-students');
    Route::delete('/classes/{class}/students/{student}', [ClassController::class, 'removeStudent'])->name('classes.remove-student');

    // User Management Routes
    Route::patch('/users/{user}/approve', [UserManagementController::class, 'approve'])->name('users.approve');
    Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');

    // Face Recognition Route
    Route::post('/face/register', [\App\Http\Controllers\Web\Portal\FaceProfileController::class, 'register'])->name('face.register');
    Route::patch('/users/{user}/deactivate', [UserManagementController::class, 'deactivate'])->name('users.deactivate');

    // Profile Route
    Route::patch('/portal/profile', [\App\Http\Controllers\Web\Portal\ProfileController::class, 'update'])->name('portal.profile.update');
});
