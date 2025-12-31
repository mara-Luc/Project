from flask import jsonify

def handle_door_open(data):
    door_id = data.get("door_id", 1)
    # TODO: implement GPIO control here
    return jsonify({"success": True, "door_id": door_id})