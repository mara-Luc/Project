import requests
from flask import jsonify

def handle_recording_event(data, backend_url):
    r = requests.post(f"{backend_url}/api/recordings", json=data)
    return jsonify(r.json())