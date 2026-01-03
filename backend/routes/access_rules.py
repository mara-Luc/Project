from flask import Blueprint, request, jsonify
from database import db_session
from models.access_rule import AccessRule
access_rules_bp = Blueprint("access_rules", __name__)
@access_rules_bp.post("/")
def create_rule():
    data = request.json
    rule = AccessRule(
        user_id=data["user_id"],
        camera_group_id=data.get("camera_group_id"),
        allowed_from=data["allowed_from"],
        allowed_to=data["allowed_to"],
        days_of_week=data["days_of_week"],
        valid_from=data["valid_from"],
        valid_to=data.get("valid_to")
    )
    db_session.add(rule)
    db_session.commit()
    return jsonify({"id": rule.id})
