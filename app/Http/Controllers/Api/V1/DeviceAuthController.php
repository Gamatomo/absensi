<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\DeviceLoginRequest;
use App\Models\Device;
use Illuminate\Http\JsonResponse;

class DeviceAuthController extends Controller
{
    public function login(DeviceLoginRequest $request): JsonResponse
    {
        $device = Device::query()->where('serial_number', $request->string('device_serial'))->firstOrFail();

        if (! $device->is_active) {
            return response()->json(['message' => 'Device disabled'], 403);
        }

        $token = $device->createToken('rpi-token')->plainTextToken;

        return response()->json([
            'message' => 'Device authenticated',
            'token' => $token,
            'device' => [
                'id' => $device->id,
                'name' => $device->name,
                'serial_number' => $device->serial_number,
            ],
        ]);
    }
}
