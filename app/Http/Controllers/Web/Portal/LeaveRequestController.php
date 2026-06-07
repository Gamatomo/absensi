<?php

namespace App\Http\Controllers\Web\Portal;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LeaveRequestController extends Controller
{
    public function store(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'reason' => 'required|string|max:255',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'description' => 'nullable|string|max:1000',
        ]);

        try {
            $leaveRequest = LeaveRequest::create([
                'user_id' => $user->id,
                'reason' => $validated['reason'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'description' => $validated['description'] ?? null,
                'status' => 'pending',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan izin telah dikirim',
                'data' => $leaveRequest,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim pengajuan: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function approve(Request $request, $id)
    {
        $user = $request->user();

        // Only admin can approve
        if ($user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk mengsetujui',
            ], 403);
        }

        try {
            $leaveRequest = LeaveRequest::findOrFail($id);
            $leaveRequest->update(['status' => 'approved']);

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan telah disetujui',
                'data' => $leaveRequest,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengsetujui: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function reject(Request $request, $id)
    {
        $user = $request->user();

        // Only admin can reject
        if ($user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk menolak',
            ], 403);
        }

        try {
            $leaveRequest = LeaveRequest::findOrFail($id);
            $leaveRequest->update(['status' => 'rejected']);

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan telah ditolak',
                'data' => $leaveRequest,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menolak: ' . $e->getMessage(),
            ], 500);
        }
    }
}
