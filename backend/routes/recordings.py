from flask import Blueprint, request, jsonify
from database import db_session
from models.recording import Recording
recordings_bp = Blueprint("recordings", __name__)
@recordings_bp.post("/")
def add_recording():
    data = request.json
    rec = Recording(
        camera_id=data["camera_id"],
        file_path=data["file_path"],
        start_time=data["start_time"],
        end_time=data["end_time"],
        duration_sec=data["duration_sec"]
    )
    db_session.add(rec)
    db_session.commit()
    return jsonify({"id": rec.id})