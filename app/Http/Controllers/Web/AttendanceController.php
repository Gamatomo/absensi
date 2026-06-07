<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AttendanceEvent;
use App\Models\AttendanceRecord;
use App\Models\RfidCard;
use App\Models\FaceProfile;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class AttendanceController extends Controller
{
    public function index(): View
    {
        $records = AttendanceRecord::query()->with('user')->latest('attendance_date')->paginate(20);
        return view('pages.attendance.index', compact('records'));
    }

    /**
     * Show RFID check-in page
     */
    public function rfidCheckIn(): View
    {
        $latestEvent = AttendanceEvent::latest()->first();
        return view('attendance.rfid-check-in', [
            'latestEvent' => $latestEvent,
            'eventDate' => $latestEvent?->event_date ?? now()->format('Y-m-d'),
        ]);
    }

    /**
     * Handle RFID card verification
     */
    public function rfidVerify(Request $request): JsonResponse
    {
        $request->validate([
            'card_id' => 'required|string',
        ]);

        $cardId = $request->input('card_id');

        // Find RFID card
        $rfidCard = RfidCard::where('card_id', $cardId)
            ->where('is_active', true)
            ->first();

        if (!$rfidCard) {
            return response()->json([
                'success' => false,
                'message' => 'Kartu tidak ditemukan atau tidak aktif',
                'type' => 'error',
            ], 404);
        }

        // Find student or teacher
        $student = Student::find($rfidCard->student_id);
        $teacher = Teacher::find($rfidCard->teacher_id);

        if (!$student && !$teacher) {
            return response()->json([
                'success' => false,
                'message' => 'Data pengguna tidak ditemukan',
                'type' => 'error',
            ], 404);
        }

        $user = $student ? $student->user : ($teacher ? $teacher->user : null);

        if (!$user || !$user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Pengguna tidak aktif',
                'type' => 'error',
            ], 403);
        }

        // Get or create today's attendance event
        $event = AttendanceEvent::whereDate('event_date', now()->toDateString())->first();

        if (!$event) {
            return response()->json([
                'success' => false,
                'message' => 'Event absensi hari ini belum dibuat',
                'type' => 'error',
            ], 404);
        }

        // Check if already checked in today
        $existingRecord = AttendanceRecord::where('attendance_event_id', $event->id)
            ->where(function ($q) use ($student, $teacher) {
                if ($student) {
                    $q->where('student_id', $student->id);
                }
                if ($teacher) {
                    $q->orWhere('teacher_id', $teacher->id);
                }
            })
            ->first();

        if ($existingRecord) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah tercatat masuk hari ini',
                'type' => 'info',
                'name' => $user->name,
                'time' => $existingRecord->check_in_time,
            ]);
        }

        // Create attendance record
        $record = AttendanceRecord::create([
            'attendance_event_id' => $event->id,
            'student_id' => $student?->id,
            'teacher_id' => $teacher?->id,
            'check_in_time' => now(),
            'status' => 'present',
            'verification_method' => 'rfid',
            'device_id' => $request->input('device_id', 'kiosk-rfid-01'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Selamat datang, ' . $user->name,
            'type' => 'success',
            'name' => $user->name,
            'time' => $record->check_in_time->format('H:i:s'),
        ]);
    }

    /**
     * Show face recognition page
     */
    public function faceRecognition(): View
    {
        $latestEvent = AttendanceEvent::latest()->first();
        return view('attendance.face-recognition', [
            'latestEvent' => $latestEvent,
            'eventDate' => $latestEvent?->event_date ?? now()->format('Y-m-d'),
        ]);
    }

    /**
     * Handle face recognition verification
     */
    public function faceVerify(Request $request): JsonResponse
    {
        $request->validate([
            'face_id' => 'required|string',
        ]);

        $faceId = $request->input('face_id');

        // Find face profile
        $faceProfile = FaceProfile::where('face_id', $faceId)
            ->where('is_verified', true)
            ->first();

        if (!$faceProfile) {
            return response()->json([
                'success' => false,
                'message' => 'Wajah tidak dikenali atau belum diverifikasi',
                'type' => 'error',
            ], 404);
        }

        // Find student or teacher
        $student = Student::find($faceProfile->student_id);
        $teacher = Teacher::find($faceProfile->teacher_id);

        if (!$student && !$teacher) {
            return response()->json([
                'success' => false,
                'message' => 'Data pengguna tidak ditemukan',
                'type' => 'error',
            ], 404);
        }

        $user = $student ? $student->user : ($teacher ? $teacher->user : null);

        if (!$user || !$user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Pengguna tidak aktif',
                'type' => 'error',
            ], 403);
        }

        // Get or create today's attendance event
        $event = AttendanceEvent::whereDate('event_date', now()->toDateString())->first();

        if (!$event) {
            return response()->json([
                'success' => false,
                'message' => 'Event absensi hari ini belum dibuat',
                'type' => 'error',
            ], 404);
        }

        // Check if already checked in today
        $existingRecord = AttendanceRecord::where('attendance_event_id', $event->id)
            ->where(function ($q) use ($student, $teacher) {
                if ($student) {
                    $q->where('student_id', $student->id);
                }
                if ($teacher) {
                    $q->orWhere('teacher_id', $teacher->id);
                }
            })
            ->first();

        if ($existingRecord) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah tercatat masuk hari ini',
                'type' => 'info',
                'name' => $user->name,
                'time' => $existingRecord->check_in_time,
            ]);
        }

        // Create attendance record
        $record = AttendanceRecord::create([
            'attendance_event_id' => $event->id,
            'student_id' => $student?->id,
            'teacher_id' => $teacher?->id,
            'check_in_time' => now(),
            'status' => 'present',
            'verification_method' => 'face_recognition',
            'device_id' => $request->input('device_id', 'kiosk-face-01'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Selamat datang, ' . $user->name,
            'type' => 'success',
            'name' => $user->name,
            'time' => $record->check_in_time->format('H:i:s'),
        ]);
    }
}
