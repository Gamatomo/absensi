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

        // Check if this UID is already assigned to someone else AND active
        $existingCard = RfidCard::where('uid', $uid)->where('status', 'active')->first();
        if ($existingCard && $existingCard->user_id !== $user->id) {
            return back()->with('error', "Kartu RFID '{$uid}' sudah digunakan oleh {$existingCard->user->name}.");
        }

        // Revoke user's current active card if it's a different card
        RfidCard::where('user_id', $user->id)
            ->where('status', 'active')
            ->where('uid', '!=', $uid)
            ->update([
                'status' => 'revoked',
                'revoked_at' => now(),
            ]);

        // Update existing card record or create a new one to respect the unique constraint
        RfidCard::updateOrCreate(
            ['uid' => $uid],
            [
                'user_id' => $user->id,
                'status' => 'active',
                'assigned_at' => now(),
                'revoked_at' => null,
            ]
        );

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
