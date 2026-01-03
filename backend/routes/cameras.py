from flask import Blueprint, request, jsonify
from database import db_session
from models.camera import Camera
cameras_bp = Blueprint("cameras", __name__)
@cameras_bp.get("/")
def list_cameras():
    cams = db_session.query(Camera).all()
    return jsonify([{
        "id": c.id,
        "name": c.name,
        "status": c.status,
        "location": c.location
    } for c in cams])
@cameras_bp.post("/")
def add_camera():
    data = request.json
    cam = Camera(
        name=data["name"],
        rtsp_url=data["rtsp_url"],
        location=data.get("location")
    )
    db_session.add(cam)
    db_session.commit()
    return jsonify({"id": cam.id})
