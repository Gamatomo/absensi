<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function index(): View
    {
        $records = AttendanceRecord::query()->with('user')->latest('attendance_date')->paginate(20);
        return view('pages.attendance.index', compact('records'));
    }
}
