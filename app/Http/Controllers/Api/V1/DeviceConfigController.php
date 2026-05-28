<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class DeviceConfigController extends Controller
{
    public function show(): JsonResponse
    {
        return response()->json([
            'face_threshold' => 0.85,
            'check_in_start' => '06:00:00',
            'late_after' => '07:30:00',
            'timezone' => config('app.timezone'),
        ]);
    }
}
