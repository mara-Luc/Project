from flask import Flask, render_template, request, jsonify
import sqlite3

app = Flask(__name__)
DB = "database.db"

# ---------------------------
# Database helper
# ---------------------------
def get_db():
    conn = sqlite3.connect(DB)
    conn.row_factory = sqlite3.Row
    return conn

# ---------------------------
# Admin page
# ---------------------------
@app.route("/admin")
def admin_manage():
    db = get_db()
    users = db.execute("SELECT * FROM users").fetchall()
    return render_template("admin_manage.html", users=users)

# ---------------------------
# Add user
# ---------------------------
@app.route("/api/user/add", methods=["POST"])
def add_user():
    data = request.json
    db = get_db()
    db.execute(
        "INSERT INTO users (firstname, lastname, username, department, role) VALUES (?, ?, ?, ?, ?)",
        (data["firstname"], data["lastname"], data["username"], data["department"], data["role"])
    )
    db.commit()
    return jsonify(success=True)

# ---------------------------
# Edit user
# ---------------------------
@app.route("/api/user/edit/<int:user_id>", methods=["POST"])
def edit_user(user_id):
    data = request.json
    db = get_db()
    db.execute(
        """UPDATE users
           SET firstname=?, lastname=?, department=?, role=?
           WHERE id=?""",
        (data["firstname"], data["lastname"], data["department"], data["role"], user_id)
    )
    db.commit()
    return jsonify(success=True)

# ---------------------------
# Delete user
# ---------------------------
@app.route("/api/user/delete/<int:user_id>", methods=["POST"])
def delete_user(user_id):
    db = get_db()
    db.execute("DELETE FROM users WHERE id=?", (user_id,))
    db.commit()
    return jsonify(success=True)

if __name__ == "__main__":
    app.run(debug=True)
