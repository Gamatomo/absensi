<?php

namespace App\Http\Controllers\Web\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use App\Models\FaceProfile;

class FaceProfileController extends Controller
{
    /**
     * Handle the face registration process.
     * Receives 5 base64 images from the frontend, sends them to the Python AI microservice,
     * and stores the resulting embedding in the database.
     */
    public function register(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'images' => 'required|array|size:5',
            'images.*' => 'required|string',
        ]);

        try {
            // Send the images to our Python Microservice
            $response = Http::timeout(60)->post('http://127.0.0.1:8001/register_face', [
                'images' => $validated['images'],
            ]);

            if (!$response->successful()) {
                $errorMsg = $response->json('detail') ?? 'Gagal menghubungi server AI (Timeout/Error).';
                return response()->json([
                    'success' => false,
                    'message' => $errorMsg,
                ], 400);
            }

            $data = $response->json();

            if (!$data['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memproses wajah.',
                ], 400);
            }

            // Invalidate old active profiles
            FaceProfile::where('user_id', $user->id)
                ->where('is_active', true)
                ->update(['is_active' => false]);

            // Save the new embedding to the database
            FaceProfile::create([
                'user_id' => $user->id,
                'profile_key' => 'FACE-' . Str::upper(Str::random(10)),
                'embedding_hash' => json_encode($data['embedding']),
                'samples_count' => 5,
                'is_active' => true,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Wajah berhasil didaftarkan. Anda dapat menggunakan pengenalan wajah untuk absensi.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses data: ' . $e->getMessage(),
            ], 500);
        }
    }
}
