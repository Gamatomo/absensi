import datetime
import uuid
import requests

API_BASE = "https://your-domain.com/api/v1"
TOKEN = "sanctum_device_token_here"

payload = {
    "device_serial": "RPI-LAB-A-01",
    "idempotency_key": str(uuid.uuid4()),
    "captured_at": datetime.datetime.utcnow().isoformat() + "Z",
    "rfid_uid": "04AABBCCDD",
    "face_result": "match",
    "face_confidence": 96.2,
    "image_ref": "events/2026-05-28/frame001.jpg",
    "metadata": {"camera": "ok", "rfid_reader": "ok"}
}

response = requests.post(
    f"{API_BASE}/device/attendance-events",
    json=payload,
    headers={"Authorization": f"Bearer {TOKEN}", "Accept": "application/json"},
    timeout=10,
)

print(response.status_code)
print(response.text)
