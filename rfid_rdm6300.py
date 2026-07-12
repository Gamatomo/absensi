#!/usr/bin/env python3
"""
RDM6300 RFID Reader Service & WebSocket Server
===============================================
Reads 125kHz RFID cards via RDM6300 (UART Serial) on Raspberry Pi.
1. Sends the card UID to the Laravel attendance API (for check-ins).
2. Broadcasts the UID over a WebSocket (for the Admin Scan Mode).

Prerequisites:
  sudo apt install python3-serial python3-requests
  or
  pip3 install pyserial requests

Usage:
  python3 rfid_rdm6300.py
"""

import serial
import datetime
import os
import time
import requests

API_BASE_URL = os.environ.get("API_BASE_URL", "https://your-vps-domain.com")

def send_to_api(uid):
    url = f"{API_BASE_URL}/api/v1/device/scan"
    try:
        response = requests.post(url, json={"uid": uid}, timeout=3)
        if response.status_code == 200:
            print("  ✅ Sent to VPS")
        else:
            print(f"  ❌ VPS Error: {response.status_code}")
    except Exception as e:
        print(f"  ❌ Failed to send to VPS: {e}")

# ─── Configuration ───────────────────────────────────────────
SERIAL_PORT = os.environ.get("SERIAL_PORT", "/dev/serial0")
COOLDOWN_SECONDS = 3
# ─────────────────────────────────────────────────────────────

def read_serial_loop():
    """Continuously read from the RDM6300 via UART."""
    try:
        ser = serial.Serial(SERIAL_PORT, 9600, timeout=1)
        print(f"[INFO] RDM6300 Serial opened on {SERIAL_PORT}")
    except Exception as e:
        print(f"[ERROR] Could not open serial port {SERIAL_PORT}: {e}")
        print("Make sure you enabled serial hardware in raspi-config!")
        return

    last_card_id = None
    last_time = 0

    while True:
        try:
            # RDM6300 sends 14 bytes per tag:
            # [0x02] [10 ASCII Hex Data] [2 ASCII Hex Checksum] [0x03]
            data = ser.read(14)
            if len(data) == 14 and data[0] == 0x02 and data[13] == 0x03:
                card_id = data[1:11].decode('ascii')
                
                current_time = time.time()
                # Debounce: skip if same card scanned within cooldown period
                if card_id == last_card_id and (current_time - last_time) < COOLDOWN_SECONDS:
                    continue
                
                last_card_id = card_id
                last_time = current_time

                timestamp = datetime.datetime.now().strftime("%H:%M:%S")
                print(f"\n[{timestamp}] 🔖 Card detected: {card_id}")

                # Send to VPS
                send_to_api(card_id)

        except Exception as e:
            print(f"[ERROR] Serial read error: {e}")
            time.sleep(1)

def main():
    print("=" * 50)
    print("  RDM6300 RFID Cloud Scanner")
    print(f"  Target VPS: {API_BASE_URL}")
    print("=" * 50)
    print("[INFO] Press Ctrl+C to stop")

    read_serial_loop()

if __name__ == "__main__":
    try:
        main()
    except KeyboardInterrupt:
        print("\n[INFO] Service stopped.")
