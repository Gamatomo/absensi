<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\AttendanceRecord;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Show the dashboard
     */
    public function index()
    {
        $students = Student::all();
        $attendanceRecords = AttendanceRecord::with('student')->get();
        
        // Calculate statistics
        $totalStudents = $students->count();
        $totalRecords = $attendanceRecords->count();
        
        // Today's attendance (use query builder before get())
        $today = today();
        $todayPresent = AttendanceRecord::whereDate('timestamp', $today)->where('status', 'present')->count();
        $todayLate = AttendanceRecord::whereDate('timestamp', $today)->where('status', 'late')->count();
        $todayAbsent = AttendanceRecord::whereDate('timestamp', $today)->where('status', 'absent')->count();

        return view('dashboard', [
            'students' => $students,
            'attendanceRecords' => $attendanceRecords,
            'totalStudents' => $totalStudents,
            'totalRecords' => $totalRecords,
            'todayPresent' => $todayPresent,
            'todayLate' => $todayLate,
            'todayAbsent' => $todayAbsent,
        ]);
    }

    /**
     * Store a new student
     */
    public function storeStudent(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:students',
            'card_id' => 'nullable|string|unique:students',
            'face_id' => 'nullable|string|unique:students',
            'department' => 'required|string',
            'enrolled_date' => 'nullable|date',
        ]);

        Student::create($validated);

        return redirect()->back()->with('success', 'Student added successfully!');
    }

    /**
     * Store attendance record
     */
    public function storeAttendance(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'timestamp' => 'required|date',
            'method' => 'required|in:face,card',
            'status' => 'required|in:present,late,absent',
            'location' => 'nullable|string',
        ]);

        AttendanceRecord::create($validated);

        return redirect()->back()->with('success', 'Attendance record added successfully!');
    }

    /**
     * Show the kiosk card scan page
     */
    public function showKiosk()
    {
        return view('presence.kiosk');
    }

    /**
     * Handle RFID card scan and continue to face recognition
     */
    public function handleCardScan(Request $request)
    {
        $validated = $request->validate([
            'card_id' => 'required|string',
            'location' => 'nullable|string',
        ]);

        $student = Student::where('card_id', $validated['card_id'])->first();

        if (! $student) {
            return redirect()->route('kiosk')->with('error', 'Kartu RFID tidak dikenali. Pastikan siswa telah terdaftar dengan Card ID yang benar.');
        }

        return redirect()->route('kiosk.face', $student->id);
    }

    /**
     * Show the face recognition confirmation page
     */
    public function showFaceRecognition(Student $student)
    {
        return view('presence.face-recognition', [
            'student' => $student,
        ]);
    }

    /**
     * Confirm face recognition and store attendance
     */
    public function confirmFaceRecognition(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'face_id' => 'required|string',
            'location' => 'nullable|string',
        ]);

        $student = Student::findOrFail($validated['student_id']);

        if ($student->face_id !== $validated['face_id']) {
            return redirect()->back()->with('error', 'Wajah tidak cocok. Silakan coba lagi.');
        }

        $status = $this->computeAttendanceStatus();

        AttendanceRecord::create([
            'student_id' => $student->id,
            'timestamp' => now(),
            'method' => 'face',
            'status' => $status,
            'location' => $validated['location'] ?? null,
        ]);

        return redirect()->route('kiosk')->with('success', "Presensi siswa {$student->name} berhasil dicatat sebagai {$status}.");
    }

    protected function computeAttendanceStatus(): string
    {
        return now()->hour >= 8 ? 'late' : 'present';
    }

    /**
     * Bulk upload students from file
     */
    public function uploadStudents(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');
        
        $header = fgetcsv($handle);
        $count = 0;

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 4) continue;

            try {
                Student::create([
                    'name' => $row[0],
                    'email' => $row[1],
                    'department' => $row[2],
                    'card_id' => $row[3] ?? null,
                    'face_id' => $row[4] ?? null,
                    'enrolled_date' => $row[5] ?? null,
                ]);
                $count++;
            } catch (\Exception $e) {
                // Skip duplicates
                continue;
            }
        }

        fclose($handle);

        return redirect()->back()->with('success', "$count students imported successfully!");
    }

    /**
     * Delete a student
     */
    public function deleteStudent($id)
    {
        Student::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Student deleted successfully!');
    }

    /**
     * Delete an attendance record
     */
    public function deleteAttendance($id)
    {
        AttendanceRecord::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Attendance record deleted successfully!');
    }
}

