<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use Illuminate\View\View;

class LeaveRequestController extends Controller
{
    public function index(): View
    {
        $requests = LeaveRequest::query()->with('user')->latest()->paginate(20);
        return view('pages.attendance.leave-requests', compact('requests'));
    }
}
