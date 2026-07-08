<?php

namespace App\Http\Controllers\Web\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function update(Request $request)
    {
        $user = $request->user();
        $role = $user->role;

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string',
        ];

        if ($role === 'student') {
            $rules['nisn'] = 'nullable|string|max:255';
        } elseif ($role === 'teacher') {
            $rules['subject'] = 'nullable|string|max:255';
        } elseif ($role === 'parent') {
            $rules['relationship'] = 'nullable|string|max:255';
        }

        $validated = $request->validate($rules);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
        ]);

        if ($role === 'student' && $user->student) {
            $user->student->update([
                'nisn' => $validated['nisn'] ?? null,
            ]);
        } elseif ($role === 'teacher' && $user->teacher) {
            $user->teacher->update([
                'subject' => $validated['subject'] ?? null,
            ]);
        } elseif ($role === 'parent' && $user->parentGuardian) {
            $user->parentGuardian->update([
                'relationship' => $validated['relationship'] ?? null,
            ]);
        }

        return back()->with('status', 'profile-updated');
    }
}
