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
            'department_id' => 'nullable|integer|exists:departments,id',
            'nisn' => 'nullable|string|max:255',
            'subject' => 'nullable|string|max:255',
            'student_id' => 'nullable|integer|exists:students,id',
            'class_id' => 'nullable|integer|exists:school_classes,id',
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

            if ($user->role === 'student' && !empty($validated['class_id'])) {
                $schoolClass = \App\Models\SchoolClass::find($validated['class_id']);
                if ($schoolClass) {
                    $validated['department_id'] = $schoolClass->department_id;
                }
            }

            match ($user->role) {
                'student' => tap(Student::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'student_number' => 'STU'.str_pad((string) $user->id, 4, '0', STR_PAD_LEFT),
                        'nisn' => $validated['nisn'] ?? null,
                        'department_id' => $validated['department_id'] ?? null,
                        'enrolled_date' => now(),
                    ]
                ), function($student) use ($validated) {
                    if (!empty($validated['class_id'])) {
                        $student->classes()->sync([$validated['class_id']]);
                    }
                }),
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

    public function bulkApprove(Request $request): JsonResponse
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'integer|exists:users,id',
        ]);

        $users = User::whereIn('id', $validated['user_ids'])
            ->where('is_active', false)
            ->whereIn('role', ['student', 'teacher'])
            ->get();

        $approvedCount = 0;

        foreach ($users as $user) {
            try {
                match ($user->role) {
                    'student' => Student::updateOrCreate(
                        ['user_id' => $user->id],
                        [
                            'student_number' => 'STU'.str_pad((string) $user->id, 4, '0', STR_PAD_LEFT),
                            'enrolled_date' => now(),
                        ]
                    ),
                    'teacher' => Teacher::updateOrCreate(
                        ['user_id' => $user->id],
                        [
                            'teacher_number' => 'TCH'.str_pad((string) $user->id, 4, '0', STR_PAD_LEFT),
                            'enrolled_date' => now(),
                        ]
                    ),
                };
                $user->update(['is_active' => true]);
                $approvedCount++;
            } catch (\Exception $e) {
                // skip failed users
            }
        }

        return response()->json([
            'success' => true,
            'message' => "$approvedCount pengguna berhasil diverifikasi",
        ]);
    }

    public function bulkReject(Request $request): JsonResponse
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'integer|exists:users,id',
        ]);

        $rejectedCount = User::whereIn('id', $validated['user_ids'])
            ->where('is_active', false)
            ->update(['is_active' => false]); // Actually we might want to delete them or just keep them inactive. The current reject just does `update(['is_active' => false])`

        // Wait, the `reject` method does `$user->update(['is_active' => false]);`. 
        // If they are already inactive, this is a no-op but it signifies rejection in the UI. 
        // If we want to actually reject, we might delete them? The user's code just updates is_active=false. 
        // Let's just do the same.

        return response()->json([
            'success' => true,
            'message' => count($validated['user_ids']) . " pengguna berhasil ditolak",
        ]);
    }
}
