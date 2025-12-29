import serial
import time
import subprocess
import requests
import mysql.connector

# Setup serial connection
ser = serial.Serial('/dev/ttyUSB0', baudrate=2400, timeout=1)

# Setup database connection
conn = mysql.connector.connect(
    host="localhost",
    user="your_db_user",
    password="your_db_password",
    database="your_db_name"
)
cursor = conn.cursor()

# Helper: send message to serial
def send_serial(message):
    ser.write(message.encode())

# Helper: start raspivid
def start_recording():
    subprocess.Popen(["raspivid", "-o", "/var/www/html/live.h264", "-t", "0"])
    requests.post("http://10.0.0.184:5555/notify.php", data={"event": "recording_started"})

# Helper: stop raspivid
def stop_recording():
    subprocess.call(["pkill", "-f", "raspivid"])

# Main loop
try:
    while True:
        input_char = ser.read().decode()

        if input_char == "V":
            start_recording()

        elif input_char == "R":
            uid = ser.read(8).decode()
            cursor.execute("SELECT pin FROM users WHERE uid = %s", (uid,))
            result = cursor.fetchone()

            if result:
                pin = result[0]
                send_serial("P" + pin)
            else:
                send_serial("X")

        elif input_char == "W":
            stop_recording()

        time.sleep(0.5)

except KeyboardInterrupt:
    print("Stopping script...")

finally:
    ser.close()
    cursor.close()
    conn.close()