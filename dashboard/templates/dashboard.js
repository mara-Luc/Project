const socket = io("http://localhost:5000");
let audioEnabled = false;

function loadView(view) {
    document.getElementById("view-title").innerText =
        view === "events" ? "Real-Time Events" :
        view.charAt(0).toUpperCase() + view.slice(1);
    const container = document.getElementById("view-container");
    if (view === "events") loadEvents(container);
    if (view === "cameras") loadCameras(container);
    if (view === "users") loadUsers(container);
    if (view === "recordings") loadRecordings(container);
    if (view === "access") loadAccessRules(container);
    if (view === "health") loadHealth(container);
}

function notify(message, success=true) {
    const div = document.createElement("div");
    div.className = "event-card";
    div.style.borderLeftColor = success ? "#4caf50" : "#f44336";
    div.innerHTML = `<strong>Notice:</strong> ${message}`;
    document.body.appendChild(div);
    setTimeout(() => div.remove(), 3000);
}

function openDoor() {
    fetch("/api/door/open", {
        method: "POST",
        headers: {"Content-Type": "application/json"},
        body: JSON.stringify({door_id: 1})
    }).then(r => r.json()).then(res => {
        const status = document.getElementById("door-status");
        if (res.success) {
            status.innerText = "Door: Opened";
            status.classList.add("health-ok");
            setTimeout(() => status.innerText = "Door: Idle", 3000);
            notify("Door opened", true);
        } else {
            status.innerText = "Door: Error";
            status.classList.add("health-bad");
            notify("Door open failed", false);
        }
    });
}

/* Events */
function loadEvents(container) {
    fetch("/api/events/latest").then(r => r.json()).then(events => {
        container.innerHTML = "";
        events.forEach(addEventCard);
    });
}
function addEventCard(event) {
    const container = document.getElementById("view-container");
    const div = document.createElement("div");
    div.className = "event-card";
    div.innerHTML = `
        <strong>${event.type.toUpperCase()}</strong><br>
        User: ${event.user || "Unknown"}<br>
        Camera: ${event.camera || "N/A"}<br>
        Success: ${event.success}<br>
        <small>${event.timestamp || new Date().toLocaleString()}</small>
    `;
    container.prepend(div);
}
socket.on("event", data => {
    if (document.getElementById("view-title").innerText !== "Real-Time Events") {
        loadView("events");
    }
    addEventCard({
        type: data.type,
        user: data.user,
        camera: data.camera,
        success: data.success,
        timestamp: new Date().toLocaleString()
    });
    notify(`Event: ${data.type}`, data.success);
});

/* Cameras */
function loadCameras(container) {
    fetch("/api/cameras/").then(r => r.json()).then(cams => {
        container.innerHTML = `<div id="camera-grid"></div>`;
        const grid = document.getElementById("camera-grid");
        cams.forEach(c => {
            const div = document.createElement("div");
            div.className = "camera-tile";
            div.innerHTML = `
                <h4>${c.name} (${c.status})</h4>
                <div class="camera-preview">Preview for camera ${c.id}</div>
                <small>${c.location || ""}</small>
            `;
            grid.appendChild(div);
        });
    });
}

/* Users */
function loadUsers(container) {
    fetch("/api/users/").then(r => r.json()).then(users => {
        container.innerHTML = `
            <table class="table">
                <tr><th>ID</th><th>Name</th><th>Status</th></tr>
                ${users.map(u => `
                    <tr><td>${u.id}</td><td>${u.name}</td><td>${u.status}</td></tr>
                `).join("")}
            </table>
        `;
    });
}

/* Recordings */
function loadRecordings(container) {
    fetch("/api/recordings/").then(r => r.json()).then(recs => {
        container.innerHTML = `
            <table class="table">
                <tr><th>ID</th><th>Camera</th><th>Start</th><th>End</th><th>File</th></tr>
                ${recs.map(r => `
                    <tr>
                        <td>${r.id}</td>
                        <td>${r.camera_id}</td>
                        <td>${r.start_time}</td>
                        <td>${r.end_time}</td>
                        <td>${r.file_path}</td>
                    </tr>
                `).join("")}
            </table>
        `;
    });
}

/* Access rules */
function loadAccessRules(container) {
    fetch("/api/access/").then(r => r.json()).then(rules => {
        container.innerHTML = `
            <table class="table">
                <tr><th>User</th><th>Group</th><th>From</th><th>To</th><th>Days</th></tr>
                ${rules.map(a => `
                    <tr>
                        <td>${a.user}</td>
                        <td>${a.group}</td>
                        <td>${a.allowed_from}</td>
                        <td>${a.allowed_to}</td>
                        <td>${JSON.stringify(a.days_of_week)}</td>
                    </tr>
                `).join("")}
            </table>
        `;
    });
}

/* Health */
function loadHealth(container) {
    fetch("/api/health/latest").then(r => r.json()).then(logs => {
        container.innerHTML = `
            <table class="table">
                <tr><th>Device</th><th>ID</th><th>Status</th><th>Message</th><th>Time</th></tr>
                ${logs.map(l => `
                    <tr>
                        <td>${l.device_type}</td>
                        <td>${l.device_id}</td>
                        <td>${l.status}</td>
                        <td>${l.message}</td>
                        <td>${l.timestamp}</td>
                    </tr>
                `).join("")}
            </table>
        `;
    });
}
socket.on("health", data => {
    if (document.getElementById("view-title").innerText === "Device Health") {
        loadHealth(document.getElementById("view-container"));
    }
    notify(`Health: ${data.device_type} ${data.device_id} → ${data.status}`, data.status === "online");
});

window.onload = () => loadView("events");