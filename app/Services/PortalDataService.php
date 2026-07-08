<?php

namespace App\Services;

use App\Data\PortalDemoData;
use App\Models\AttendanceEvent;
use App\Models\LeaveRequest;
use App\Models\ParentGuardian;
use App\Models\Schedule;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class PortalDataService
{
    public function payload(): array
    {
        $authUser = auth()->user();

        $students = $this->students();
        $teachers = $this->teachers();
        $attendanceRecords = $this->attendanceRecords();
        $leaveRequests = $this->leaveRequests();
        $classes = $this->classes();
        $parents = $this->parents();
        $schedules = $this->schedules();
        $pendingUsers = $this->pendingUsers();

        // Resolve currentStudent/Teacher/Parent from the logged-in user
        $currentStudent = $authUser
            ? (collect($students)->firstWhere('userId', $authUser->id) ?? $students[0] ?? null)
            : ($students[0] ?? null);
        $currentTeacher = $authUser
            ? (collect($teachers)->firstWhere('userId', $authUser->id) ?? $teachers[0] ?? null)
            : ($teachers[0] ?? null);
        $currentParent = $authUser
            ? (collect($parents)->firstWhere('userId', $authUser->id) ?? $parents[0] ?? null)
            : ($parents[0] ?? null);

        return [
            'students' => $students,
            'teachers' => $teachers,
            'attendanceRecords' => $attendanceRecords,
            'leaveRequests' => $leaveRequests,
            'classes' => $classes,
            'parents' => $parents,
            'schedules' => $schedules,
            'pendingUsers' => $pendingUsers,
            'weeklyChart' => $this->weeklyChart($attendanceRecords),
            'stats' => $this->stats($students, $teachers, $attendanceRecords, $leaveRequests, count($pendingUsers)),
            'currentStudent' => $currentStudent,
            'currentTeacher' => $currentTeacher,
            'currentParent' => $currentParent,
        ];
    }

    public function students(): array
    {
        if (Student::query()->doesntExist()) {
            return PortalDemoData::students();
        }

        return Student::query()->with(['user', 'user.rfidCards', 'user.faceProfiles', 'classes'])->get()->map(function (Student $student) {
            $activeCard = $student->user?->rfidCards->firstWhere('status', 'active');
            $activeFace = $student->user?->faceProfiles->firstWhere('is_active', true);

            return [
                'id' => $student->student_number,
                'dbId' => $student->id,
                'userId' => $student->user_id,
                'isActive' => (bool) ($student->user?->is_active ?? true),
                'name' => $student->user?->name,
                'email' => $student->user?->email,
                'cardId' => $activeCard?->uid,
                'faceId' => $activeFace?->profile_key,
                'department' => $student->department,
                'enrolledDate' => $student->enrolled_date?->format('Y-m-d'),
                'nisn' => $student->nisn,
                'phone' => $student->user?->phone,
                'address' => $student->user?->address,
                'className' => $student->classes->first()?->name,
                'classId' => $student->classes->first()?->id,
            ];
        })->all();
    }

    public function teachers(): array
    {
        if (Teacher::query()->doesntExist()) {
            return PortalDemoData::teachers();
        }

        return Teacher::query()->with(['user', 'user.rfidCards', 'user.faceProfiles'])->get()->map(function (Teacher $teacher) {
            $activeCard = $teacher->user?->rfidCards->firstWhere('status', 'active');
            $activeFace = $teacher->user?->faceProfiles->firstWhere('is_active', true);

            return [
                'id' => $teacher->teacher_number,
                'userId' => $teacher->user_id,
                'isActive' => (bool) ($teacher->user?->is_active ?? true),
                'name' => $teacher->user?->name,
                'email' => $teacher->user?->email,
                'cardId' => $activeCard?->uid,
                'faceId' => $activeFace?->profile_key,
                'subject' => $teacher->subject,
                'enrolledDate' => $teacher->enrolled_date?->format('Y-m-d'),
                'phone' => $teacher->user?->phone,
                'address' => $teacher->user?->address,
            ];
        })->all();
    }

    public function attendanceRecords(): array
    {
        if (AttendanceEvent::query()->doesntExist()) {
            return PortalDemoData::attendanceRecords();
        }

        return AttendanceEvent::query()->with('user.student')->latest('captured_at')->get()->map(function (AttendanceEvent $event) {
            $studentId = $event->user?->student?->student_number ?? 'UNKNOWN';

            return [
                'id' => (string) $event->id,
                'studentId' => $studentId,
                'timestamp' => $event->captured_at?->toIso8601String(),
                'method' => $event->rfid_uid ? 'card' : 'face',
                'status' => $this->mapStatus($event),
                'location' => $event->payload['metadata']['location'] ?? null,
            ];
        })->all();
    }

    public function leaveRequests(): array
    {
        if (LeaveRequest::query()->doesntExist()) {
            return PortalDemoData::leaveRequests();
        }

        return LeaveRequest::query()->with('user.student')->latest()->get()->map(function (LeaveRequest $request) {
            return [
                'id' => (string) $request->id,
                'userId' => (string) $request->user_id,
                'studentId' => $request->user?->student?->student_number ?? (string) $request->user_id,
                'requestType' => $request->request_type,
                'userName' => $request->user?->name,
                'reason' => $request->reason,
                'startDate' => $request->start_date?->format('Y-m-d'),
                'endDate' => $request->end_date?->format('Y-m-d'),
                'description' => $request->description,
                'status' => $request->status,
                'submittedAt' => $request->created_at?->toIso8601String(),
            ];
        })->all();
    }

    public function classes(): array
    {
        if (SchoolClass::query()->doesntExist()) {
            return PortalDemoData::classes();
        }

        return SchoolClass::query()->with(['homeroomTeacher.user', 'students'])->get()->map(function (SchoolClass $class) {
            return [
                'id' => (string) $class->id,
                'name' => $class->name,
                'level' => $class->level,
                'department' => $class->department,
                'homeroomTeacherId' => $class->homeroomTeacher?->teacher_number,
                'homeroomTeacherName' => $class->homeroomTeacher?->user?->name,
                'studentCount' => $class->students->count(),
                'academicYear' => $class->academic_year,
                'room' => $class->room,
            ];
        })->all();
    }

    public function parents(): array
    {
        if (ParentGuardian::query()->doesntExist()) {
            return PortalDemoData::parents();
        }

        return ParentGuardian::query()->with(['user', 'student.user'])->get()->map(function (ParentGuardian $parent) {
            return [
                'id' => (string) $parent->id,
                'userId' => $parent->user_id,
                'isActive' => (bool) ($parent->user?->is_active ?? true),
                'name' => $parent->user?->name,
                'relationship' => $parent->relationship,
                'phone' => $parent->user?->phone,
                'email' => $parent->user?->email,
                'address' => $parent->user?->address,
                'studentId' => $parent->student?->student_number,
                'studentName' => $parent->student?->user?->name,
                'occupation' => $parent->occupation,
            ];
        })->all();
    }

    public function pendingUsers(): array
    {
        if (User::query()->doesntExist()) {
            return [];
        }

        return User::query()
            ->where('is_active', false)
            ->whereIn('role', ['student', 'teacher', 'parent'])
            ->with(['student', 'teacher', 'parentGuardian'])
            ->latest()
            ->get()
            ->map(function (User $user) {
                $hasProfile = match ($user->role) {
                    'student' => $user->student !== null,
                    'teacher' => $user->teacher !== null,
                    'parent' => $user->parentGuardian !== null,
                    default => false,
                };

                return [
                    'id' => (string) $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'registeredAt' => $user->created_at?->toIso8601String(),
                    'hasProfile' => $hasProfile,
                ];
            })
            ->all();
    }

    public function schedules(): array
    {
        if (Schedule::query()->doesntExist()) {
            return PortalDemoData::schedules();
        }

        $dayMap = [
            'monday' => 'Senin',
            'tuesday' => 'Selasa',
            'wednesday' => 'Rabu',
            'thursday' => 'Kamis',
            'friday' => 'Jumat',
            'saturday' => 'Sabtu',
        ];

        return Schedule::query()->with(['schoolClass', 'teacher.user'])->get()->map(function (Schedule $schedule) use ($dayMap) {
            return [
                'id' => (string) $schedule->id,
                'className' => $schedule->schoolClass?->name,
                'subject' => $schedule->subject,
                'teacherId' => $schedule->teacher?->teacher_number,
                'teacherName' => $schedule->teacher?->user?->name,
                'day' => $dayMap[$schedule->day_of_week] ?? $schedule->day_of_week,
                'startTime' => substr((string) $schedule->start_time, 0, 5),
                'endTime' => substr((string) $schedule->end_time, 0, 5),
                'room' => $schedule->room,
            ];
        })->all();
    }

    protected function stats(array $students, array $teachers, array $records, array $leaves, int $pendingUserCount = 0): array
    {
        $today = collect($records)->filter(fn ($r) => Carbon::parse($r['timestamp'])->isToday());
        $presentToday = $today->where('status', 'present')->count();
        $totalStudents = count($students);

        return [
            'totalStudents' => $totalStudents,
            'totalTeachers' => count($teachers),
            'presentToday' => $presentToday,
            'attendanceRate' => $totalStudents > 0 ? number_format(($presentToday / $totalStudents) * 100, 1) : '0',
            'totalRecords' => count($records),
            'totalLeaveRequests' => count($leaves),
            'pendingUserCount' => $pendingUserCount,
        ];
    }

    protected function weeklyChart(array $records): array
    {
        $days = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
        $data = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dayRecords = collect($records)->filter(fn ($r) => Carbon::parse($r['timestamp'])->isSameDay($date));

            $data[] = [
                'day' => $days[$date->dayOfWeek].' '.$date->format('j/n'),
                'present' => $dayRecords->where('status', 'present')->count(),
                'late' => $dayRecords->where('status', 'late')->count(),
                'absent' => $dayRecords->where('status', 'absent')->count(),
            ];
        }

        return $data;
    }

    protected function mapStatus(AttendanceEvent $event): string
    {
        if ($event->face_result === 'mismatch') {
            return 'absent';
        }

        $time = $event->captured_at?->format('H:i:s') ?? '00:00:00';

        return $time > '07:30:00' ? 'late' : 'present';
    }
}
