#!/usr/bin/env python3
import serial
import time
import subprocess
import requests
import mysql.connector
import signal
import sys
import threading

# === Configuration ===
SERIAL_DEVICE = "/dev/ttyUSB0"   # adjust as needed
BAUDRATE = 2400
SERIAL_TIMEOUT = 0.1             # seconds for read timeout

MYSQL_CONFIG = {
    #"host": "localhost",
    #"user": "your_db_user",
    #"password": "your_db_password",
    #"database": "your_database",
    #"port": 3306,

    "host": "localhost",
    "user": "php",
    "password": "Voidnull0",
    "database": "ringDB",
    "port": 3306,
    
    #from PHP method
    #$db_host = "localhost";
    #$db_username = "php";
    #$db_password = "Voidnull0";
    #$db_name = "ringDB";
}

NOTIFY_URL = "http://10.0.0.184:5555/notify.php"  # adjust if needed
RASPIVID_OUTPUT = "/var/www/html/live.h264"

# === Globals ===
running = True
ser = None
db_conn = None
db_lock = threading.Lock()  # protect DB access if needed

# === Signal handlers for graceful shutdown ===
def stop_running(signum, frame):
    global running
    running = False

signal.signal(signal.SIGINT, stop_running)
signal.signal(signal.SIGTERM, stop_running)

# === Helper functions ===
def start_raspivid():
    # Start raspivid in background, writing to RASPIVID_OUTPUT
    # -t 0 means run until killed
    # Use subprocess.Popen so we can kill it later with pkill -f raspivid (like PHP)
    try:
        # Launch in background; redirect stdout/stderr to devnull
        subprocess.Popen(
            ["raspivid", "-o", RASPIVID_OUTPUT, "-t", "0"],
            stdout=subprocess.DEVNULL,
            stderr=subprocess.DEVNULL,
            start_new_session=True
        )
    except Exception as e:
        print("Failed to start raspivid:", e)

    # Notify web server
    try:
        requests.post(NOTIFY_URL, data={"event": "recording_started"}, timeout=5)
    except Exception as e:
        print("Failed to notify server:", e)

def stop_raspivid():
    # Kill raspivid processes (mirrors pkill -f raspivid)
    try:
        subprocess.run(["pkill", "-f", "raspivid"], check=False)
    except Exception as e:
        print("Failed to stop raspivid:", e)

def read_exact_bytes(serial_port, n):
    """Read exactly n bytes (or characters) from serial, return as string.
       Returns None on timeout or error."""
    buf = bytearray()
    deadline = time.time() + 1.0  # 1 second total to gather bytes (adjust if needed)
    while len(buf) < n and time.time() < deadline:
        try:
            chunk = serial_port.read(n - len(buf))
        except Exception as e:
            print("Serial read error:", e)
            return None
        if not chunk:
            # no data available right now
            time.sleep(0.01)
            continue
        buf.extend(chunk)
    if len(buf) < n:
        return None
    try:
        return buf.decode("utf-8", errors="ignore")
    except Exception:
        return buf.decode("latin1", errors="ignore")

def query_pin_by_uid(uid):
    """Return pin string if found, else None."""
    global db_conn
    if not db_conn:
        return None
    try:
        with db_lock:
            cursor = db_conn.cursor()
            cursor.execute("SELECT pin FROM users WHERE uid = %s LIMIT 1", (uid,))
            row = cursor.fetchone()
            cursor.close()
        if row:
            return str(row[0])
        return None
    except Exception as e:
        print("Database query error:", e)
        return None

# === Main ===
def main():
    global ser, db_conn, running

    # Open serial port
    try:
        ser = serial.Serial(
            port=SERIAL_DEVICE,
            baudrate=BAUDRATE,
            bytesize=serial.EIGHTBITS,
            parity=serial.PARITY_NONE,
            stopbits=serial.STOPBITS_ONE,
            timeout=SERIAL_TIMEOUT
        )
    except Exception as e:
        print("Failed to open serial port:", e)
        sys.exit(1)

    # Connect to database
    try:
        db_conn = mysql.connector.connect(**MYSQL_CONFIG)
    except Exception as e:
        print("Failed to connect to database:", e)
        # We continue; UID lookups will fail until DB is available
        db_conn = None

    print("Started. Listening on serial:", SERIAL_DEVICE)

    try:
        while running:
            try:
                # Read one byte/char from serial
                data = ser.read(1)
            except Exception as e:
                print("Serial read exception:", e)
                data = b""

            if not data:
                time.sleep(0.1)
                continue

            try:
                cmd = data.decode("utf-8", errors="ignore")
            except Exception:
                cmd = data.decode("latin1", errors="ignore")

            cmd = cmd.strip()
            if not cmd:
                # Could be whitespace or newline; ignore
                continue

            # Debug print
            # print("Received command:", repr(cmd))

            if cmd == "V":
                # Start recording and notify
                start_raspivid()

            elif cmd == "R":
                # Read next 8 bytes as UID
                uid = read_exact_bytes(ser, 8)
                if uid is None:
                    print("Failed to read full UID from serial")
                    # Optionally send error char
                    try:
                        ser.write(b"X")
                    except Exception:
                        pass
                    continue

                uid = uid.strip()
                # Query DB for pin
                pin = query_pin_by_uid(uid)
                if pin is not None:
                    out = ("P" + pin).encode("utf-8")
                else:
                    out = b"X"
                try:
                    ser.write(out)
                except Exception as e:
                    print("Failed to write to serial:", e)

            elif cmd == "W":
                # Stop recording
                stop_raspivid()

            else:
                # Unknown command: ignore or log
                # print("Unknown command:", repr(cmd))
                pass

            # Wait half a second before next loop iteration (mirrors usleep(500000))
            time.sleep(0.5)

    finally:
        # Cleanup
        print("Shutting down...")
        try:
            if ser and ser.is_open:
                ser.close()
        except Exception:
            pass
        try:
            if db_conn:
                db_conn.close()
        except Exception:
            pass
        # Ensure raspivid stopped
        stop_raspivid()
        print("Exited cleanly.")

if __name__ == "__main__":
    main()