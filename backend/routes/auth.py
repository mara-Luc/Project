from flask import Blueprint, request, session, jsonify
from database import db_session
from models.user import User
import pyotp

auth_bp = Blueprint("auth", __name__)

@auth_bp.post("/login")
def login():
    data = request.json
    email = data.get("email")
    password = data.get("password")

    user = db_session.query(User).filter_by(email=email).one_or_none()
    if not user or not user.check_password(password):
        return jsonify({"success": False, "error": "Invalid credentials"}), 401

    if not user.two_factor_enabled:
        session["user_id"] = user.id
        session["role"] = user.role
        return jsonify({"success": True, "two_factor_required": False})

    return jsonify({"success": True, "two_factor_required": True})

@auth_bp.post("/verify-2fa")
def verify_2fa():
    data = request.json
    email = data.get("email")
    code = data.get("code")

    user = db_session.query(User).filter_by(email=email).one_or_none()
    if not user or not user.two_factor_enabled:
        return jsonify({"success": False, "error": "2FA not enabled"}), 400

    totp = pyotp.TOTP(user.two_factor_secret)
    if not totp.verify(code):
        return jsonify({"success": False, "error": "Invalid 2FA code"}), 401

    session["user_id"] = user.id
    session["role"] = user.role
    return jsonify({"success": True})

@auth_bp.post("/toggle-2fa")
def toggle_2fa():
    if "user_id" not in session:
        return jsonify({"error": "Not logged in"}), 401
    user = db_session.query(User).get(session["user_id"])
    enable = request.json.get("enable")

    if enable:
        secret = pyotp.random_base32()
        user.two_factor_secret = secret
        user.two_factor_enabled = True
        db_session.commit()
        return jsonify({"success": True, "secret": secret})
    else:
        user.two_factor_enabled = False
        user.two_factor_secret = None
        db_session.commit()
        return jsonify({"success": True})

@auth_bp.post("/logout")
def logout():
    session.clear()
    return jsonify({"success": True})