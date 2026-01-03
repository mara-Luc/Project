from flask import Blueprint, request, jsonify
from database import db_session
from models.credentials import RFIDCard
from models.event import Event
from models.user import User
from app import socketio

rfid_bp = Blueprint("rfid", __name__)

@rfid_bp.post("/scan")
def rfid_scan():
    data = request.json
    uid = data["uid"]
    camera_id = data.get("camera_id")

    card = db_session.query(RFIDCard).filter_by(uid=uid).one_or_none()
    user = card.user if card else None

    event = Event(
        event_type="rfid",
        user_id=user.id if user else None,
        camera_id=camera_id,
        credential_id=card.id if card else None,
        success=True if user else False,
        metadata={"uid": uid}
    )

    db_session.add(event)
    db_session.commit()

    socketio.emit("event", {
        "type": "rfid",
        "user": user.name if user else None,
        "camera_id": camera_id,
        "success": event.success
    })

    return jsonify({"success": event.success})
