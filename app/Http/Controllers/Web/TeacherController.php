<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use Illuminate\View\View;

class TeacherController extends Controller
{
    public function index(): View
    {
        $teachers = Teacher::query()->with('user')->latest()->paginate(15);
        return view('pages.admin.teachers.index', compact('teachers'));
    }
}
