<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\RfidCard;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class UserManagementController extends Controller
{
    /**
     * Show user edit page with RFID card management.
     */
    public function edit(int $id): View
    {
        $user = User::with(['rfidCards', 'student', 'teacher', 'faceProfiles'])->findOrFail($id);

        return view('pages.admin.users.edit', compact('user'));
    }

    /**
     * Assign an RFID card to a user.
     */
    public function assignRfid(Request $request, int $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        $request->validate([
            'uid' => 'required|string|max:255',
        ]);

        $uid = strtoupper(trim($request->input('uid')));

        // Check if this UID is already assigned to someone else
        $existingCard = RfidCard::where('uid', $uid)->where('status', 'active')->first();
        if ($existingCard && $existingCard->user_id !== $user->id) {
            return back()->with('error', "Kartu RFID '{$uid}' sudah digunakan oleh {$existingCard->user->name}.");
        }

        // Check if user already has an active card
        $currentCard = RfidCard::where('user_id', $user->id)->where('status', 'active')->first();
        if ($currentCard) {
            // Revoke old card first
            $currentCard->update([
                'status' => 'revoked',
                'revoked_at' => now(),
            ]);
        }

        // Create new RFID card
        RfidCard::create([
            'user_id' => $user->id,
            'uid' => $uid,
            'status' => 'active',
            'assigned_at' => now(),
        ]);

        return back()->with('success', "Kartu RFID '{$uid}' berhasil didaftarkan untuk {$user->name}.");
    }

    /**
     * Revoke an RFID card.
     */
    public function revokeRfid(int $id, int $cardId): RedirectResponse
    {
        $user = User::findOrFail($id);
        $card = RfidCard::where('id', $cardId)->where('user_id', $user->id)->firstOrFail();

        $card->update([
            'status' => 'revoked',
            'revoked_at' => now(),
        ]);

        return back()->with('success', "Kartu RFID '{$card->uid}' berhasil dicabut.");
    }
}
