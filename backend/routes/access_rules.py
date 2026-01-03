from flask import Blueprint, request, jsonify
from datetime import datetime
from database import db_session
from models.access_rule import AccessRule

access_rules_bp = Blueprint("access_rules", __name__)


@access_rules_bp.post("/")
def create_rule():
    data = request.get_json()

    required_fields = [
        "user_id",
        "allowed_from",
        "allowed_to",
        "days_of_week",
        "valid_from"
    ]

    for field in required_fields:
        if field not in data:
            return jsonify({"error": f"Missing field: {field}"}), 400

    try:
        rule = AccessRule(
            user_id=data["user_id"],
            camera_group_id=data.get("camera_group_id"),
            allowed_from=datetime.strptime(data["allowed_from"], "%H:%M").time(),
            allowed_to=datetime.strptime(data["allowed_to"], "%H:%M").time(),
            days_of_week=data["days_of_week"],
            valid_from=datetime.strptime(data["valid_from"], "%Y-%m-%d").date(),
            valid_to=(
                datetime.strptime(data["valid_to"], "%Y-%m-%d").date()
                if data.get("valid_to") else None
            ),
        )

        db_session.add(rule)
        db_session.commit()

        return jsonify({"id": rule.id}), 201

    except ValueError:
        return jsonify({"error": "Invalid date or time format"}), 400

    except Exception as e:
        db_session.rollback()
        return jsonify({"error": "Failed to create access rule"}), 500

