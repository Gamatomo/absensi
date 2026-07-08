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
use Illuminate\Support\Facades\Http;

class AttendanceController extends Controller
{
    public function index(): View
    {
        $records = AttendanceRecord::query()->with('user')->latest('attendance_date')->paginate(20);
        return view('pages.attendance.index', compact('records'));
    }

    /**
     * Get live list of checked-in users for today
     */
    public function liveList(): JsonResponse
    {
        $today = now()->toDateString();
        $records = AttendanceRecord::with(['user.student', 'user.teacher'])
            ->whereDate('attendance_date', $today)
            ->where('status', 'present')
            ->orderBy('check_in_time', 'desc')
            ->get();

        $students = [];
        $teachers = [];

        foreach ($records as $record) {
            $userData = [
                'name' => $record->user->name,
                'time' => $record->check_in_time ? \Carbon\Carbon::parse($record->check_in_time)->format('H:i') : '-',
            ];

            if ($record->user->role === 'student' || $record->user->student) {
                $students[] = $userData;
            } elseif ($record->user->role === 'teacher' || $record->user->teacher) {
                $teachers[] = $userData;
            }
        }

        return response()->json([
            'students' => $students,
            'teachers' => $teachers
        ]);
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
        $rfidCard = RfidCard::where('uid', $cardId)
            ->where('status', 'active')
            ->first();

        if (!$rfidCard) {
            return response()->json([
                'success' => false,
                'message' => 'Kartu tidak ditemukan atau tidak aktif',
                'type' => 'error',
            ], 404);
        }

        // The RfidCard is linked directly to the User
        $user = $rfidCard->user;

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Data pengguna tidak ditemukan',
                'type' => 'error',
            ], 404);
        }

        // We can optionally find the specific student/teacher profile
        $student = $user->student;
        $teacher = $user->teacher;

        if (!$user || !$user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Pengguna tidak aktif',
                'type' => 'error',
            ], 403);
        }

        // Check if already checked in today
        $existingRecord = AttendanceRecord::where('user_id', $user->id)
            ->whereDate('attendance_date', now()->toDateString())
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

        // Instead of logging attendance, we just return success and the profile key
        // so the frontend can redirect to the Face Recognition step.
        $faceProfile = FaceProfile::where('user_id', $user->id)
            ->where('is_active', true)
            ->first();

        if (!$faceProfile) {
            return response()->json([
                'success' => false,
                'message' => 'Kartu dikenali, namun Anda belum mendaftarkan wajah',
                'type' => 'error',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Kartu RFID Terverifikasi. Melanjutkan ke pemindaian wajah...',
            'type' => 'success',
            'name' => $user->name,
            'profile_key' => $faceProfile->profile_key,
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

    /**
     * Handle face recognition from webcam directly
     */
    public function faceVerifyCamera(Request $request): JsonResponse
    {
        $request->validate([
            'image' => 'required|string',
            'profile_key' => 'required|string',
        ]);

        $profileKey = $request->input('profile_key');

        // Get the specific face profile
        $faceProfile = FaceProfile::where('profile_key', $profileKey)
            ->where('is_active', true)
            ->first();

        if (!$faceProfile) {
            return response()->json([
                'success' => false,
                'message' => 'Profil wajah tidak valid atau tidak aktif',
                'type' => 'error',
            ], 404);
        }

        try {
            // Send to python API using the 1-to-1 verification endpoint
            $response = Http::timeout(10)->post('http://127.0.0.1:8001/verify_face', [
                'image' => $request->input('image'),
                'target_embedding' => json_decode($faceProfile->embedding_hash, true),
            ]);

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghubungi server AI',
                    'type' => 'error',
                ], 500);
            }

            $data = $response->json();

            if (!$data['success'] || !$data['match']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Wajah tidak cocok dengan identitas kartu RFID',
                    'type' => 'error',
                ], 404);
            }

            // Find user
            $user = $faceProfile->user;
            if (!$user || !$user->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pengguna tidak aktif',
                    'type' => 'error',
                ], 403);
            }
            
            $student = Student::where('user_id', $user->id)->first();
            $teacher = Teacher::where('user_id', $user->id)->first();

            // Check if already checked in today
            $existingRecord = AttendanceRecord::where('user_id', $user->id)
                ->whereDate('attendance_date', now()->toDateString())
                ->first();

            if ($existingRecord) {
                return response()->json([
                    'success' => true, // Still true so UI can show info
                    'message' => 'Anda sudah tercatat masuk hari ini',
                    'type' => 'info',
                    'name' => $user->name,
                    'time' => $existingRecord->check_in_time ? \Carbon\Carbon::parse($existingRecord->check_in_time)->format('H:i:s') : '-',
                ]);
            }

            // Create attendance record
            $record = AttendanceRecord::create([
                'user_id' => $user->id,
                'school_class_id' => $student ? $student->school_class_id : null,
                'attendance_date' => now()->toDateString(),
                'check_in_time' => now()->toTimeString(),
                'status' => 'present',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Selamat datang, ' . $user->name,
                'type' => 'success',
                'name' => $user->name,
                'time' => now()->format('H:i:s'),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kesalahan sistem: ' . $e->getMessage(),
                'type' => 'error',
            ], 500);
        }
    }
}
