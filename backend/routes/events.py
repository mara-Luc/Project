from flask import Blueprint, jsonify
from database import db_session
from models.event import Event
from models.user import User
from models.camera import Camera
events_bp = Blueprint("events", __name__)
@events_bp.get("/latest")
def latest_events():
    events = db_session.query(Event).order_by(Event.timestamp.desc()).limit(50)
    return jsonify([{
        "id": e.id,
        "timestamp": e.timestamp.isoformat(),
        "type": e.event_type,
        "user": e.user.name if e.user else None,
        "camera": e.camera.name if e.camera else None,
        "success": e.success
    } for e in events])
