import requests
from flask import jsonify

def handle_camera_event(data, backend_url):
    r = requests.post(f"{backend_url}/api/events/camera", json=data)
    return jsonify(r.json())