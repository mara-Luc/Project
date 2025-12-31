from app import socketio

def emit_event(event_type, user=None, camera=None, success=False, metadata=None):
    payload = {
        "type": event_type,
        "user": user,
        "camera": camera,
        "success": success,
        "metadata": metadata or {}
    }
    socketio.emit("event", payload, broadcast=True)

def emit_health(device_type, device_id, status, message):
    socketio.emit("health", {
        "device_type": device_type,
        "device_id": device_id,
        "status": status,
        "message": message
    }, broadcast=True)