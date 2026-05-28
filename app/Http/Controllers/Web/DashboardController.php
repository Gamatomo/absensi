<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('pages.admin.dashboard', [
            'studentCount' => Student::count(),
            'teacherCount' => Teacher::count(),
            'todayAttendance' => AttendanceRecord::whereDate('attendance_date', now()->toDateString())->count(),
            'lateCount' => AttendanceRecord::whereDate('attendance_date', now()->toDateString())->where('status', 'late')->count(),
        ]);
    }
}
