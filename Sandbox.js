function deleteUser(id) {
    fetch(`/api/user/delete/${id}`, { method: "POST" })
        .then(() => location.reload());
}

function editUser(id) {
    const firstname = prompt("First name:");
    const lastname = prompt("Last name:");
    const department = prompt("Department:");
    const role = prompt("Role:");

    fetch(`/api/user/edit/${id}`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ firstname, lastname, department, role })
    }).then(() => location.reload());
}

function openAdd() {
    const firstname = prompt("First name:");
    const lastname = prompt("Last name:");
    const username = prompt("Username:");
    const department = prompt("Department:");
    const role = prompt("Role:");

    fetch("/api/user/add", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ firstname, lastname, username, department, role })
    }).then(() => location.reload());
}

// Search filter
document.getElementById("searchInput").addEventListener("keyup", function () {
    const filter = this.value.toLowerCase();
    document.querySelectorAll(".user-row").forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(filter)
            ? ""
            : "none";
    });
});
