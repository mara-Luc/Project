from flask import Blueprint, request, jsonify
import requests
from auth_utils import require_role

door_bp = Blueprint("door", __name__)
PI_CONTROLLER_URL = "http://raspberrypi.local:7000"

@door_bp.post("/open")
@require_role("admin", "operator")
def open_door():
    data = request.json or {}
    door_id = data.get("door_id", 1)
    try:
        r = requests.post(f"{PI_CONTROLLER_URL}/controller/door", json={"door_id": door_id})
        return jsonify({"success": True, "door_id": door_id})
    except Exception as e:
        return jsonify({"success": False, "error": str(e)}), 500