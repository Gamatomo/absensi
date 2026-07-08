<?php

namespace App\Http\Controllers\Web\Portal;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    public function store(Request $request)
    {
        $user = $request->user();

        // Only admin can create classes
        if ($user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk membuat kelas',
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:school_classes,name',
            'level' => 'required|string|in:X,XI,XII',
            'department' => 'required|string|max:255',
            'room' => 'required|string|max:255',
            'academic_year' => 'required|string|max:255',
            'homeroom_teacher_id' => 'nullable|exists:teachers,id',
        ]);

        try {
            $class = SchoolClass::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Kelas berhasil dibuat',
                'data' => $class,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat kelas: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function addStudents(Request $request, SchoolClass $class)
    {
        $user = $request->user();

        if ($user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin',
            ], 403);
        }

        $validated = $request->validate([
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'exists:students,id',
        ]);

        try {
            // Sync without detaching — only adds new ones
            $class->students()->syncWithoutDetaching($validated['student_ids']);

            return response()->json([
                'success' => true,
                'message' => count($validated['student_ids']) . ' siswa berhasil ditambahkan ke kelas ' . $class->name,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan siswa: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function removeStudent(Request $request, SchoolClass $class, Student $student)
    {
        $user = $request->user();

        if ($user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin',
            ], 403);
        }

        try {
            $class->students()->detach($student->id);

            return response()->json([
                'success' => true,
                'message' => 'Siswa berhasil dihapus dari kelas ' . $class->name,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus siswa: ' . $e->getMessage(),
            ], 500);
        }
    }
}

