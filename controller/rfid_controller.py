import requests
from flask import jsonify

def handle_rfid_scan(data, backend_url):
    uid = data["uid"]
    camera_id = data.get("camera_id")
    payload = {"uid": uid, "camera_id": camera_id}

    r = requests.post(f"{backend_url}/api/rfid/scan", json=payload)
    res = r.json()
    return jsonify({
        "success": res["success"],
        "action": "unlock" if res["success"] else "deny"
    })