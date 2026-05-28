<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreAttendanceEventRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'device_serial' => ['required', 'string', 'exists:devices,serial_number'],
            'idempotency_key' => ['required', 'string', 'max:255'],
            'captured_at' => ['required', 'date'],
            'rfid_uid' => ['nullable', 'string', 'max:100'],
            'face_result' => ['required', 'in:match,mismatch,not_provided'],
            'face_confidence' => ['nullable', 'numeric', 'between:0,100'],
            'metadata' => ['nullable', 'array'],
            'image_ref' => ['nullable', 'string', 'max:500'],
        ];
    }
}
