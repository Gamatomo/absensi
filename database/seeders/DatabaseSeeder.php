<?php

namespace Database\Seeders;

use App\Models\ParentGuardian;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin Seeder
        $this->seedAdmin();

        // Teacher (Guru) Seeder
        $this->seedTeacher();

        // Student (Siswa) Seeder
        $this->seedStudent();

        // Parent (Orang Tua) Seeder
        $this->seedParent();
    }

    private function seedAdmin(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@sekolah.ac.id'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'phone' => '081234567890',
                'address' => 'Jalan Sudirman No. 123, Jakarta',
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'phone' => '089876543210',
                'address' => 'Jalan Ahmad Yani No. 456, Bandung',
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );
    }

    private function seedTeacher(): void
    {
        $teacherUser = User::query()->updateOrCreate(
            ['email' => 'guru@sekolah.ac.id'],
            [
                'name' => 'Budi Santoso',
                'password' => Hash::make('password'),
                'role' => 'teacher',
                'phone' => '082345678901',
                'address' => 'Jalan Merdeka No. 789, Surabaya',
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );

        Teacher::query()->updateOrCreate(
            ['user_id' => $teacherUser->id],
            [
                'teacher_number' => 'TCH001',
                'subject' => 'Matematika',
                'enrolled_date' => now()->subYears(5),
            ]
        );

        $teacherUser2 = User::query()->updateOrCreate(
            ['email' => 'guru2@sekolah.ac.id'],
            [
                'name' => 'Siti Nurhaliza',
                'password' => Hash::make('password'),
                'role' => 'teacher',
                'phone' => '083456789012',
                'address' => 'Jalan Gajah Mada No. 321, Medan',
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );

        Teacher::query()->updateOrCreate(
            ['user_id' => $teacherUser2->id],
            [
                'teacher_number' => 'TCH002',
                'subject' => 'Bahasa Indonesia',
                'enrolled_date' => now()->subYears(3),
            ]
        );
    }

    private function seedStudent(): void
    {
        $studentUser = User::query()->updateOrCreate(
            ['email' => 'siswa@sekolah.ac.id'],
            [
                'name' => 'Ahmad Rizki',
                'password' => Hash::make('password'),
                'role' => 'student',
                'phone' => '085678901234',
                'address' => 'Jalan Diponegoro No. 654, Yogyakarta',
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );

        Student::query()->updateOrCreate(
            ['user_id' => $studentUser->id],
            [
                'student_number' => 'STU001',
                'nisn' => '0012345678',
                'department' => 'Teknik Informatika',
                'enrolled_date' => now()->subYears(2),
            ]
        );

        $studentUser2 = User::query()->updateOrCreate(
            ['email' => 'siswa2@sekolah.ac.id'],
            [
                'name' => 'Nur Azizah',
                'password' => Hash::make('password'),
                'role' => 'student',
                'phone' => '086789012345',
                'address' => 'Jalan Gatot Subroto No. 987, Semarang',
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );

        Student::query()->updateOrCreate(
            ['user_id' => $studentUser2->id],
            [
                'student_number' => 'STU002',
                'nisn' => '0087654321',
                'department' => 'Teknik Mesin',
                'enrolled_date' => now()->subYear(),
            ]
        );
    }

    private function seedParent(): void
    {
        $parentUser = User::query()->updateOrCreate(
            ['email' => 'parent@sekolah.ac.id'],
            [
                'name' => 'Hendra Wijaya',
                'password' => Hash::make('password'),
                'role' => 'parent',
                'phone' => '087890123456',
                'address' => 'Jalan Sultan Agung No. 147, Palembang',
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );

        // Get first student for parent relationship
        $student = Student::query()->first();
        if ($student) {
            ParentGuardian::query()->updateOrCreate(
                ['user_id' => $parentUser->id, 'student_id' => $student->id],
                [
                    'relationship' => 'Father',
                    'occupation' => 'Engineer',
                ]
            );
        }

        $parentUser2 = User::query()->updateOrCreate(
            ['email' => 'parent2@sekolah.ac.id'],
            [
                'name' => 'Dewi Lestari',
                'password' => Hash::make('password'),
                'role' => 'parent',
                'phone' => '088901234567',
                'address' => 'Jalan Imam Bonjol No. 258, Banjarmasin',
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );

        // Get second student for parent relationship
        $student2 = Student::query()->skip(1)->first();
        if ($student2) {
            ParentGuardian::query()->updateOrCreate(
                ['user_id' => $parentUser2->id, 'student_id' => $student2->id],
                [
                    'relationship' => 'Mother',
                    'occupation' => 'Doctor',
                ]
            );
        }
    }
}
