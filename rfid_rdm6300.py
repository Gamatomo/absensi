#!/usr/bin/env python3
"""
RDM6300 RFID Reader Service & WebSocket Server
===============================================
Reads 125kHz RFID cards via RDM6300 (UART Serial) on Raspberry Pi.
1. Sends the card UID to the Laravel attendance API (for check-ins).
2. Broadcasts the UID over a WebSocket (for the Admin Scan Mode).

Prerequisites:
  sudo apt install python3-serial python3-websockets python3-requests
  or
  pip3 install pyserial websockets requests

Usage:
  python3 rfid_rdm6300.py
"""

import asyncio
import websockets
import serial
import json
import datetime
import os
import threading
import time

# ─── Configuration ───────────────────────────────────────────
SERIAL_PORT = os.environ.get("SERIAL_PORT", "/dev/serial0")
COOLDOWN_SECONDS = 3
# ─────────────────────────────────────────────────────────────

clients = set()

async def register(websocket):
    """Register a new WebSocket client."""
    clients.add(websocket)
    try:
        await websocket.wait_closed()
    finally:
        clients.remove(websocket)

async def broadcast(message):
    """Broadcast a message to all connected WebSocket clients."""
    if not clients:
        return
    for client in list(clients):
        try:
            await client.send(message)
        except websockets.exceptions.ConnectionClosed:
            pass

def read_serial_loop(loop):
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

                # Broadcast to any open web browser
                asyncio.run_coroutine_threadsafe(
                    broadcast(json.dumps({"uid": card_id})), loop
                )

        except Exception as e:
            print(f"[ERROR] Serial read error: {e}")
            time.sleep(1)

async def main():
    print("=" * 50)
    print("  RDM6300 RFID WebSocket Service")
    print("=" * 50)

    # Start the serial reader in a separate background thread
    loop = asyncio.get_running_loop()
    thread = threading.Thread(target=read_serial_loop, args=(loop,), daemon=True)
    thread.start()

    # Start the WebSocket server
    ws_port = 8765
    async with websockets.serve(register, "0.0.0.0", ws_port):
        print(f"[INFO] WebSocket Server listening on ws://0.0.0.0:{ws_port}")
        print("[INFO] Press Ctrl+C to stop")
        await asyncio.Future()  # run forever

if __name__ == "__main__":
    try:
        asyncio.run(main())
    except KeyboardInterrupt:
        print("\n[INFO] Service stopped.")
