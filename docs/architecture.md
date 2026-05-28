# Attendance Architecture

## Separation of concerns
- Laravel handles web dashboard, auth, attendance reports, user and class management, and API persistence.
- Raspberry Pi handles RFID scanning, camera capture, and local face verification.
- Pi pushes event payloads to Laravel `/api/v1/device/attendance-events`.

## Attendance flow
1. Pi reads RFID and captures face result.
2. Pi sends signed/authenticated payload with idempotency key.
3. Laravel stores immutable `attendance_events` row.
4. Laravel upserts normalized `attendance_records` row.
5. Dashboard and reports consume `attendance_records`.

## Security
- Sanctum token auth for devices.
- Idempotency key to avoid duplicates from network retries.
- Full payload retained in JSON for audit.
