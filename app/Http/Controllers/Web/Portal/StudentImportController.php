<?php

namespace App\Http\Controllers\Web\Portal;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class StudentImportController extends Controller
{
    public function store(Request $request)
    {
        $user = $request->user();

        // Only admin can import
        if ($user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk mengunggah data',
            ], 403);
        }

        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
        ]);

        try {
            $file = $request->file('file');
            $stream = fopen($file->getRealPath(), 'r');

            $headers = fgetcsv($stream);
            $importedCount = 0;
            $errors = [];
            $row = 1;

            while (($data = fgetcsv($stream)) !== false) {
                $row++;

                if (empty(array_filter($data))) {
                    continue;
                }

                try {
                    $record = array_combine($headers, $data);

                    // Create or update user
                    $appUser = User::updateOrCreate(
                        ['email' => $record['email'] ?? null],
                        [
                            'name' => $record['name'] ?? 'Unknown',
                            'role' => 'student',
                            'phone' => $record['phone'] ?? null,
                            'address' => $record['address'] ?? null,
                            'email_verified_at' => now(),
                            'is_active' => true,
                            'password' => Hash::make('password123'),
                        ]
                    );

                    // Create or update student
                    Student::updateOrCreate(
                        ['user_id' => $appUser->id],
                        [
                            'student_number' => $record['id'] ?? 'STU' . str_pad($appUser->id, 4, '0', STR_PAD_LEFT),
                            'nisn' => $record['nisn'] ?? null,
                            'department' => $record['department'] ?? null,
                            'enrolled_date' => isset($record['enrolledDate']) ? date('Y-m-d', strtotime($record['enrolledDate'])) : now(),
                        ]
                    );

                    $importedCount++;
                } catch (\Exception $e) {
                    $errors[] = "Baris {$row}: " . $e->getMessage();
                }
            }

            fclose($stream);

            return response()->json([
                'success' => true,
                'message' => "Berhasil mengimpor {$importedCount} siswa",
                'imported' => $importedCount,
                'errors' => $errors,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengunggah file: ' . $e->getMessage(),
            ], 500);
        }
    }
}
