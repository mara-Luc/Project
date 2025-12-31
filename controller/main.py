from flask import Flask, request, jsonify
import requests
from rfid_controller import handle_rfid_scan
from pin_controller import handle_pin_entry
from camera_controller import handle_camera_event
from recording_controller import handle_recording_event
from door_controller import handle_door_open

BACKEND_URL = "http://localhost:5000"

app = Flask(__name__)

@app.post("/controller/rfid")
def controller_rfid():
    return handle_rfid_scan(request.json, BACKEND_URL)

@app.post("/controller/pin")
def controller_pin():
    return handle_pin_entry(request.json, BACKEND_URL)

@app.post("/controller/camera")
def controller_camera():
    return handle_camera_event(request.json, BACKEND_URL)

@app.post("/controller/recording")
def controller_recording():
    return handle_recording_event(request.json, BACKEND_URL)

@app.post("/controller/door")
def controller_door():
    return handle_door_open(request.json)

if __name__ == "__main__":
    app.run(host="0.0.0.0", port=7000)