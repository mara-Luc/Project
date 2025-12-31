import requests
from flask import jsonify

def handle_pin_entry(data, backend_url):
    payload = {
        "pin_hash": data["pin_hash"],
        "camera_id": data.get("camera_id")
    }
    r = requests.post(f"{backend_url}/api/pin/enter", json=payload)
    res = r.json()
    return jsonify({
        "success": res["success"],
        "action": "unlock" if res["success"] else "deny"
    })