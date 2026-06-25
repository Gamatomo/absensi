<?php

namespace App\Http\Controllers\Web\Portal;

use App\Http\Controllers\Controller;
use App\Models\ParentGuardian;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    public function approve(Request $request, User $user): JsonResponse
    {
        if ($request->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk memverifikasi pengguna',
            ], 403);
        }

        if ($user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Pengguna sudah diverifikasi',
            ], 422);
        }

        if (! in_array($user->role, ['student', 'teacher', 'parent'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Peran pengguna tidak dapat diverifikasi',
            ], 422);
        }

        $validated = $request->validate([
            'department' => 'nullable|string|max:255',
            'nisn' => 'nullable|string|max:255',
            'subject' => 'nullable|string|max:255',
            'student_id' => 'nullable|integer|exists:students,id',
            'relationship' => 'nullable|string|max:255',
            'occupation' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
        ]);

        if ($user->role === 'parent') {
            if (empty($validated['student_id']) || empty($validated['relationship'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Orang tua harus dihubungkan ke siswa dan hubungan keluarga',
                ], 422);
            }
        }

        try {
            if (! empty($validated['phone']) || ! empty($validated['address'])) {
                $user->update(array_filter([
                    'phone' => $validated['phone'] ?? null,
                    'address' => $validated['address'] ?? null,
                ], fn ($value) => $value !== null));
            }

            match ($user->role) {
                'student' => Student::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'student_number' => 'STU'.str_pad((string) $user->id, 4, '0', STR_PAD_LEFT),
                        'nisn' => $validated['nisn'] ?? null,
                        'department' => $validated['department'] ?? null,
                        'enrolled_date' => now(),
                    ]
                ),
                'teacher' => Teacher::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'teacher_number' => 'TCH'.str_pad((string) $user->id, 4, '0', STR_PAD_LEFT),
                        'subject' => $validated['subject'] ?? null,
                        'enrolled_date' => now(),
                    ]
                ),
                'parent' => ParentGuardian::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'student_id' => $validated['student_id'],
                    ],
                    [
                        'relationship' => $validated['relationship'],
                        'occupation' => $validated['occupation'] ?? null,
                    ]
                ),
            };

            $user->update(['is_active' => true]);

            return response()->json([
                'success' => true,
                'message' => 'Pengguna berhasil diverifikasi',
                'data' => $user->fresh(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memverifikasi pengguna: '.$e->getMessage(),
            ], 500);
        }
    }

    public function reject(Request $request, User $user): JsonResponse
    {
        if ($request->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk menolak pengguna',
            ], 403);
        }

        if ($user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Pengguna sudah diverifikasi',
            ], 422);
        }

        try {
            $user->update(['is_active' => false]);

            return response()->json([
                'success' => true,
                'message' => 'Pengguna ditolak',
                'data' => $user->fresh(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menolak pengguna: '.$e->getMessage(),
            ], 500);
        }
    }

    public function deactivate(Request $request, User $user): JsonResponse
    {
        if ($request->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk menonaktifkan pengguna',
            ], 403);
        }

        if (! $user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Pengguna sudah nonaktif',
            ], 422);
        }

        if ($user->role === 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Akun admin tidak dapat dinonaktifkan',
            ], 422);
        }

        try {
            $user->update(['is_active' => false]);

            return response()->json([
                'success' => true,
                'message' => 'Pengguna berhasil dinonaktifkan',
                'data' => $user->fresh(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menonaktifkan pengguna: '.$e->getMessage(),
            ], 500);
        }
    }
}
