<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeviceHeartbeatController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate(['device_serial' => ['required', 'string', 'exists:devices,serial_number']]);
        Device::query()->where('serial_number', $validated['device_serial'])->update(['last_seen_at' => now()]);
        return response()->json(['message' => 'Heartbeat accepted']);
    }
}
