from flask import Flask, render_template
from flask_socketio import SocketIO
from database import db_session
from routes.users import users_bp
from routes.rfid import rfid_bp
from routes.pin import pin_bp
from routes.cameras import cameras_bp
from routes.recordings import recordings_bp
from routes.events import events_bp
from routes.access_rules import access_rules_bp
from routes.health import health_bp
from routes.auth import auth_bp
from routes.door import door_bp
from auth_utils import require_login

socketio = SocketIO(cors_allowed_origins="*")

def create_app():
    app = Flask(__name__)
    app.secret_key = "CHANGE_ME"

    app.register_blueprint(users_bp, url_prefix="/api/users")
    app.register_blueprint(rfid_bp, url_prefix="/api/rfid")
    app.register_blueprint(pin_bp, url_prefix="/api/pin")
    app.register_blueprint(cameras_bp, url_prefix="/api/cameras")
    app.register_blueprint(recordings_bp, url_prefix="/api/recordings")
    app.register_blueprint(events_bp, url_prefix="/api/events")
    app.register_blueprint(access_rules_bp, url_prefix="/api/access")
    app.register_blueprint(health_bp, url_prefix="/api/health")
    app.register_blueprint(auth_bp, url_prefix="/api/auth")
    app.register_blueprint(door_bp, url_prefix="/api/door")

    @app.get("/dashboard")
    @require_login
    def dashboard():
        return render_template("dashboard.html")

    @app.get("/login")
    def login_page():
        return render_template("login.html")

    socketio.init_app(app)
    return app

app = create_app()

if __name__ == "__main__":
    socketio.run(app, host="0.0.0.0", port=5000)