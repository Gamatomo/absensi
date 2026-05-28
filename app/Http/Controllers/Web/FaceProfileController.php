<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\FaceProfile;
use Illuminate\View\View;

class FaceProfileController extends Controller
{
    public function index(): View
    {
        $profiles = FaceProfile::query()->with('user')->latest()->paginate(15);
        return view('pages.attendance.face-profiles', compact('profiles'));
    }
}
