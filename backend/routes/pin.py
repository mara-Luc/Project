from flask import Blueprint, request, jsonify
from database import db_session
from models.credentials import PINCode
from models.event import Event
from app import socketio

pin_bp = Blueprint("pin", __name__)

@pin_bp.post("/enter")
def pin_enter():
    data = request.json
    pin_hash = data["pin_hash"]
    camera_id = data.get("camera_id")

    pin = db_session.query(PINCode).filter_by(pin_hash=pin_hash).one_or_none()
    user = pin.user if pin else None

    event = Event(
        event_type="pin",
        user_id=user.id if user else None,
        camera_id=camera_id,
        credential_id=pin.id if pin else None,
        success=True if user else False,
        metadata={"pin_hash": pin_hash}
    )

    db_session.add(event)
    db_session.commit()

    socketio.emit("event", {
        "type": "pin",
        "user": user.name if user else None,
        "camera_id": camera_id,
        "success": event.success
    })

    return jsonify({"success": event.success})
