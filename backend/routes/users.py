from flask import Blueprint, request, jsonify
from database import db_session
from models.user import User, UserPhoto
users_bp = Blueprint("users", __name__)
@users_bp.get("/")
def list_users():
    users = db_session.query(User).all()
    return jsonify([{"id": u.id, "name": u.name, "status": u.status} for u in users])
@users_bp.post("/")
def create_user():
    data = request.json
    user = User(name=data["name"], email=data.get("email"))
    db_session.add(user)
    db_session.commit()
    return jsonify({"id": user.id, "message": "User created"})