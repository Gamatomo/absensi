<?php

namespace App\Data;

class PortalDemoData
{
    public static function students(): array
    {
        return [
            [
                'id' => 'STU001',
                'name' => 'Ahmad Fauzi',
                'email' => 'ahmad.fauzi@sekolah.ac.id',
                'cardId' => 'CARD-001',
                'faceId' => 'FACE-001',
                'department' => 'Teknik Alat Berat',
                'enrolledDate' => '2024-01-15',
                'nisn' => '0012345678',
                'phone' => '081234567890',
                'address' => 'Jl. Merdeka No. 123, Jakarta Pusat',
            ],
            [
                'id' => 'STU002',
                'name' => 'Siti Rahayu',
                'email' => 'siti.rahayu@sekolah.ac.id',
                'cardId' => 'CARD-002',
                'faceId' => 'FACE-002',
                'department' => 'Teknik Informatika',
                'enrolledDate' => '2024-01-15',
                'nisn' => '0012345679',
                'phone' => '081234567891',
                'address' => 'Jl. Sudirman No. 45, Jakarta Selatan',
            ],
        ];
    }

    public static function teachers(): array
    {
        return [
            [
                'id' => 'TCH001',
                'name' => 'Budi Santoso',
                'email' => 'budi.santoso@sekolah.ac.id',
                'cardId' => 'CARD-T001',
                'faceId' => 'FACE-T001',
                'subject' => 'Matematika',
                'enrolledDate' => '2023-08-01',
                'phone' => '082345678901',
                'address' => 'Jl. Sudirman No. 45, Jakarta Selatan',
            ],
            [
                'id' => 'TCH002',
                'name' => 'Dewi Anggraeni',
                'email' => 'dewi.a@sekolah.ac.id',
                'cardId' => 'CARD-T002',
                'faceId' => 'FACE-T002',
                'subject' => 'Bahasa Indonesia',
                'enrolledDate' => '2023-08-01',
                'phone' => '082345678902',
                'address' => 'Jl. Gatot Subroto No. 10, Jakarta Barat',
            ],
        ];
    }

    public static function attendanceRecords(): array
    {
        return [
            ['id' => 'ATT001', 'studentId' => 'STU001', 'timestamp' => '2026-05-26T07:30:00', 'method' => 'face', 'status' => 'present', 'location' => 'Gedung A - Lantai 1'],
            ['id' => 'ATT002', 'studentId' => 'STU001', 'timestamp' => '2026-05-23T07:45:00', 'method' => 'card', 'status' => 'late', 'location' => 'Gedung A - Lantai 1'],
            ['id' => 'ATT003', 'studentId' => 'STU001', 'timestamp' => '2026-05-22T07:25:00', 'method' => 'face', 'status' => 'present', 'location' => 'Gedung A - Lantai 1'],
            ['id' => 'ATT004', 'studentId' => 'STU001', 'timestamp' => '2026-05-21T07:20:00', 'method' => 'face', 'status' => 'present', 'location' => 'Gedung A - Lantai 1'],
            ['id' => 'ATT005', 'studentId' => 'STU002', 'timestamp' => '2026-05-26T07:35:00', 'method' => 'card', 'status' => 'present', 'location' => 'Gedung B - Lantai 2'],
        ];
    }

    public static function leaveRequests(): array
    {
        return [
            [
                'id' => 'LR001',
                'studentId' => 'STU001',
                'reason' => 'Sakit',
                'startDate' => '2026-05-01',
                'endDate' => '2026-05-02',
                'description' => 'Demam dan flu, perlu istirahat di rumah',
                'status' => 'approved',
                'submittedAt' => '2026-04-30T10:00:00',
            ],
        ];
    }

    public static function classes(): array
    {
        return [
            ['id' => 'CLS001', 'name' => 'X-TAB-A', 'level' => 'X', 'department' => 'Teknik Alat Berat', 'homeroomTeacherId' => 'TCH001', 'homeroomTeacherName' => 'Budi Santoso', 'studentCount' => 32, 'academicYear' => '2025/2026', 'room' => 'Gedung A - Ruang 101'],
            ['id' => 'CLS002', 'name' => 'X-TI-A', 'level' => 'X', 'department' => 'Teknik Informatika', 'homeroomTeacherId' => 'TCH002', 'homeroomTeacherName' => 'Dewi Anggraeni', 'studentCount' => 30, 'academicYear' => '2025/2026', 'room' => 'Gedung B - Ruang 201'],
            ['id' => 'CLS003', 'name' => 'XI-TAB-A', 'level' => 'XI', 'department' => 'Teknik Alat Berat', 'homeroomTeacherId' => 'TCH001', 'homeroomTeacherName' => 'Budi Santoso', 'studentCount' => 28, 'academicYear' => '2025/2026', 'room' => 'Gedung A - Ruang 102'],
        ];
    }

    public static function parents(): array
    {
        return [
            [
                'id' => 'PAR001',
                'name' => 'Hendra Fauzi',
                'relationship' => 'Ayah',
                'phone' => '081298765432',
                'email' => 'hendra.f@gmail.com',
                'address' => 'Jl. Merdeka No. 123, Jakarta Pusat',
                'studentId' => 'STU001',
                'studentName' => 'Ahmad Fauzi',
                'occupation' => 'Pegawai Swasta',
            ],
        ];
    }

    public static function schedules(): array
    {
        return [
            ['id' => 'SCH001', 'className' => 'X-TAB-A', 'subject' => 'Matematika', 'teacherId' => 'TCH001', 'teacherName' => 'Budi Santoso', 'day' => 'Senin', 'startTime' => '07:00', 'endTime' => '08:30', 'room' => 'Gedung A - 101'],
            ['id' => 'SCH002', 'className' => 'X-TAB-A', 'subject' => 'Bahasa Indonesia', 'teacherId' => 'TCH002', 'teacherName' => 'Dewi Anggraeni', 'day' => 'Senin', 'startTime' => '08:30', 'endTime' => '10:00', 'room' => 'Gedung A - 101'],
            ['id' => 'SCH003', 'className' => 'X-TAB-A', 'subject' => 'Matematika', 'teacherId' => 'TCH001', 'teacherName' => 'Budi Santoso', 'day' => 'Rabu', 'startTime' => '07:00', 'endTime' => '08:30', 'room' => 'Gedung A - 101'],
            ['id' => 'SCH004', 'className' => 'X-TAB-A', 'subject' => 'Bahasa Indonesia', 'teacherId' => 'TCH002', 'teacherName' => 'Dewi Anggraeni', 'day' => 'Jumat', 'startTime' => '09:00', 'endTime' => '10:30', 'room' => 'Gedung A - 101'],
            ['id' => 'SCH005', 'className' => 'X-TI-A', 'subject' => 'Pemrograman Dasar', 'teacherId' => 'TCH002', 'teacherName' => 'Dewi Anggraeni', 'day' => 'Selasa', 'startTime' => '07:00', 'endTime' => '09:00', 'room' => 'Lab Komputer 1'],
            ['id' => 'SCH006', 'className' => 'X-TI-A', 'subject' => 'Matematika', 'teacherId' => 'TCH001', 'teacherName' => 'Budi Santoso', 'day' => 'Kamis', 'startTime' => '08:00', 'endTime' => '09:30', 'room' => 'Gedung B - 201'],
        ];
    }

    public static function weeklyChart(): array
    {
        $records = self::attendanceRecords();
        $days = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
        $data = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dayRecords = collect($records)->filter(function ($record) use ($date) {
                return \Carbon\Carbon::parse($record['timestamp'])->isSameDay($date);
            });

            $data[] = [
                'day' => $days[$date->dayOfWeek].' '.$date->format('j/n'),
                'present' => $dayRecords->where('status', 'present')->count(),
                'late' => $dayRecords->where('status', 'late')->count(),
                'absent' => $dayRecords->where('status', 'absent')->count(),
            ];
        }

        return $data;
    }
}
