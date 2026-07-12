<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreAttendanceEventRequest;
use App\Models\AttendanceEvent;
use App\Models\AttendanceRecord;
use App\Models\Device;
use App\Models\RfidCard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DeviceAttendanceController extends Controller
{
    public function storeScan(Request $request): JsonResponse
    {
        $request->validate(['uid' => 'required|string']);
        
        // Store in cache for 30 seconds
        Cache::put('device_last_scan', strtoupper($request->input('uid')), 30);
        
        return response()->json(['message' => 'Scan stored successfully']);
    }

    public function getLastScan(): JsonResponse
    {
        $uid = Cache::get('device_last_scan');
        
        if ($uid) {
            // Clear it so it's not read twice
            Cache::forget('device_last_scan');
            return response()->json(['uid' => $uid]);
        }
        
        return response()->json(['uid' => null]);
    }
    public function store(StoreAttendanceEventRequest $request): JsonResponse
    {
        $existing = AttendanceEvent::query()->where('idempotency_key', $request->string('idempotency_key'))->first();
        if ($existing) {
            return response()->json(['message' => 'Already processed', 'event_id' => $existing->id]);
        }

        $device = Device::query()->where('serial_number', $request->string('device_serial'))->firstOrFail();
        $card = $request->filled('rfid_uid')
            ? RfidCard::query()->where('uid', $request->string('rfid_uid'))->where('status', 'active')->first()
            : null;

        $event = AttendanceEvent::query()->create([
            'device_id' => $device->id,
            'user_id' => $card?->user_id,
            'rfid_uid' => $request->input('rfid_uid'),
            'face_result' => $request->string('face_result'),
            'face_confidence' => $request->input('face_confidence'),
            'captured_at' => $request->date('captured_at'),
            'idempotency_key' => $request->string('idempotency_key'),
            'payload' => ['metadata' => $request->input('metadata', []), 'image_ref' => $request->input('image_ref')],
        ]);

        if ($card?->user_id && $request->string('face_result') !== 'mismatch') {
            AttendanceRecord::query()->updateOrCreate(
                ['user_id' => $card->user_id, 'attendance_date' => now()->toDateString(), 'school_class_id' => null],
                [
                    'check_in_time' => now()->toTimeString(),
                    'status' => now()->format('H:i:s') > '07:30:00' ? 'late' : 'present',
                    'source_event_id' => $event->id,
                ]
            );
        }

        return response()->json(['message' => 'Attendance event stored', 'event_id' => $event->id], 201);
    }
}
