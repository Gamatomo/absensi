#!/usr/bin/env python3
"""
RC522 RFID Reader Service & WebSocket Server (Raspberry Pi 5 Compatible)
========================================================================
Reads 13.56MHz RFID cards via RC522 (SPI) on Raspberry Pi.
Broadcasts the UID over a WebSocket (for the Admin Scan Mode and Check-in Kiosk).

Prerequisites:
  sudo apt install python3-spidev python3-websockets
  or
  pip3 install spidev websockets

Usage:
  python3 rfid_rc522.py
"""

import asyncio
import websockets
import spidev
import json
import datetime
import threading
import time

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

# ─── RC522 Constants & Helpers ───
PCD_IDLE       = 0x00
PCD_TRANSCEIVE = 0x0C
PICC_REQIDL    = 0x26
PICC_ANTICOLL1 = 0x93

def init_rc522(spi):
    def write_reg(addr, val):
        spi.xfer2([(addr << 1) & 0x7E, val])
    def read_reg(addr):
        return spi.xfer2([((addr << 1) & 0x7E) | 0x80, 0])[1]
    def set_bit(addr, mask):
        write_reg(addr, read_reg(addr) | mask)
        
    write_reg(0x01, 0x0F)  # SoftReset
    time.sleep(0.05)
    write_reg(0x2A, 0x8D)  # TModeReg
    write_reg(0x2B, 0x3E)  # TPrescalerReg
    write_reg(0x2C, 0x00)  # TReloadRegH
    write_reg(0x2D, 0x1E)  # TReloadRegL
    write_reg(0x15, 0x40)  # TxASKReg
    write_reg(0x11, 0x3D)  # ModeReg
    set_bit(0x14, 0x03)    # Antenna on

def card_command(spi, cmd, data):
    def write_reg(addr, val):
        spi.xfer2([(addr << 1) & 0x7E, val])
    def read_reg(addr):
        return spi.xfer2([((addr << 1) & 0x7E) | 0x80, 0])[1]
    def set_bit(addr, mask):
        write_reg(addr, read_reg(addr) | mask)
    def clear_bit(addr, mask):
        write_reg(addr, read_reg(addr) & ~mask)

    write_reg(0x02, 0x77)  # ComIEnReg
    clear_bit(0x04, 0x80)  # ComIrqReg
    set_bit(0x0A, 0x80)    # FIFOLevelReg flush
    write_reg(0x01, PCD_IDLE)
    for b in data:
        write_reg(0x09, b)
    write_reg(0x01, cmd)
    if cmd == PCD_TRANSCEIVE:
        set_bit(0x0D, 0x80)
    timeout = 2000
    while timeout > 0:
        irq = read_reg(0x04)
        if irq & 0x30:
            break
        if irq & 0x01:
            return None, []
        timeout -= 1
    if timeout == 0:
        return None, []
    err = read_reg(0x06)
    if err & 0x1B:
        return None, []
    n = read_reg(0x0A)
    result = []
    for _ in range(n):
        result.append(read_reg(0x09))
    return n, result

def read_rc522_loop(loop):
    try:
        spi = spidev.SpiDev()
        spi.open(0, 0)
        spi.max_speed_hz = 1000000
        init_rc522(spi)
        print("[INFO] RC522 SPI opened and initialized successfully")
    except Exception as e:
        print(f"[ERROR] Could not open SPI: {e}")
        print("Make sure SPI is enabled in raspi-config!")
        return

    def request_card():
        def write_reg(addr, val):
            spi.xfer2([(addr << 1) & 0x7E, val])
        write_reg(0x0D, 0x07)
        n, data = card_command(spi, PCD_TRANSCEIVE, [PICC_REQIDL])
        if n is None or len(data) != 2:
            return None
        return data

    def anticoll():
        def write_reg(addr, val):
            spi.xfer2([(addr << 1) & 0x7E, val])
        write_reg(0x0D, 0x00)
        n, data = card_command(spi, PCD_TRANSCEIVE, [PICC_ANTICOLL1, 0x20])
        if n is None or len(data) != 5:
            return None
        chk = 0
        for b in data[:4]:
            chk ^= b
        if chk != data[4]:
            return None
        return data[:4]

    last_uid = None
    last_time = 0
    COOLDOWN_SECONDS = 3

    while True:
        try:
            atq = request_card()
            if atq:
                uid_bytes = anticoll()
                if uid_bytes:
                    hex_uid = ''.join(f'{b:02X}' for b in uid_bytes)
                    
                    current_time = time.time()
                    if hex_uid == last_uid and (current_time - last_time) < COOLDOWN_SECONDS:
                        time.sleep(0.1)
                        continue
                        
                    last_uid = hex_uid
                    last_time = current_time

                    timestamp = datetime.datetime.now().strftime("%H:%M:%S")
                    print(f"\n[{timestamp}] 🔖 Card detected: {hex_uid}")

                    asyncio.run_coroutine_threadsafe(
                        broadcast(json.dumps({"uid": hex_uid})), loop
                    )
            time.sleep(0.1)
        except Exception as e:
            print(f"[ERROR] RC522 read error: {e}")
            time.sleep(1)

async def main():
    print("=" * 50)
    print("  RC522 RFID WebSocket Service (Pi 5)")
    print("=" * 50)

    # Start the SPI reader in a separate background thread
    loop = asyncio.get_running_loop()
    thread = threading.Thread(target=read_rc522_loop, args=(loop,), daemon=True)
    thread.start()

    # Start the WebSocket server
    ws_port = 8765
    async with websockets.serve(register, "0.0.0.0", ws_port):
        print(f"[INFO] WebSocket Server listening on ws://0.0.0.0:{ws_port}")
        print("[INFO] Press Ctrl+C to stop")
        await asyncio.Future()

if __name__ == "__main__":
    try:
        asyncio.run(main())
    except KeyboardInterrupt:
        print("\n[INFO] Service stopped.")
