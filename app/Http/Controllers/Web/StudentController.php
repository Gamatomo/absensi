<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(): View
    {
        $students = Student::query()->with('user')->latest()->paginate(15);
        return view('pages.admin.students.index', compact('students'));
    }
}
