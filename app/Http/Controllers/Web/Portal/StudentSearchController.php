<?php

namespace App\Http\Controllers\Web\Portal;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentSearchController extends Controller
{
    public function search(Request $request): JsonResponse
    {
        $query = Student::query()->with('user');

        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        if ($request->filled('q')) {
            $searchTerm = $request->q;
            $query->where(function ($q) use ($searchTerm) {
                $q->whereHas('user', function ($userQuery) use ($searchTerm) {
                    $userQuery->where('name', 'like', "%{$searchTerm}%");
                })
                ->orWhere('nisn', 'like', "%{$searchTerm}%")
                ->orWhere('student_number', 'like', "%{$searchTerm}%");
            });
        }

        $students = $query->take(50)->get()->map(function ($student) {
            return [
                'id' => $student->id,
                'name' => $student->user ? $student->user->name : 'Unknown',
                'student_number' => $student->student_number,
                'nisn' => $student->nisn,
                'department' => $student->department,
            ];
        });

        return response()->json($students);
    }
}
