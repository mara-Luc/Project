from flask import Blueprint, request, jsonify
from database import db_session
from models.device_status import DeviceStatusLog
health_bp = Blueprint("health", __name__)
@health_bp.post("/")
def log_status():
    data = request.json
    log = DeviceStatusLog(
        device_type=data["device_type"],
        device_id=data.get("device_id"),
        status=data["status"],
        message=data.get("message")
    )
    db_session.add(log)
    db_session.commit()
    return jsonify({"id": log.id})
@health_bp.get("/latest")
def latest_status():
    logs = db_session.query(DeviceStatusLog).order_by(DeviceStatusLog.timestamp.desc()).limit(50)
    return jsonify([{
        "device_type": l.device_type,
        "device_id": l.device_id,
        "status": l.status,
        "message": l.message,
        "timestamp": l.timestamp.isoformat()
    } for l in logs])
