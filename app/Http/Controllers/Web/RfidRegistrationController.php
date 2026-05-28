<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\RfidCard;
use Illuminate\View\View;

class RfidRegistrationController extends Controller
{
    public function index(): View
    {
        $cards = RfidCard::query()->with('user')->latest()->paginate(15);
        return view('pages.attendance.rfid-registration', compact('cards'));
    }
}
