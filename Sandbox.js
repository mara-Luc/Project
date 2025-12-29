// ----------------- CARD HANDLING -----------------
let cardsContainer = document.getElementById("cardsContainer");

function addCard(uid = '', access=1, flags=0, expiry=0, authorized=true, timestamp=0) {
    const div = document.createElement("div");
    div.className = "card-item";
    div.innerHTML = `
        UID: <input type="text" class="card-uid" value="${uid}">
        Access: <input type="number" class="card-access" value="${access}" min="1">
        Flags: <input type="number" class="card-flags" value="${flags}" min="0">
        Expiry: <input type="number" class="card-expiry" value="${expiry}">
        Authorized: <input type="checkbox" class="card-auth" ${authorized?"checked":""}>
        Timestamp: <input type="number" class="card-ts" value="${timestamp}">
        <button type="button" onclick="removeCard(this)">Remove</button>
    `;
    cardsContainer.appendChild(div);
}

function removeCard(btn) {
    btn.parentElement.remove();
}

// ----------------- FORM SUBMISSION -----------------
function submitForm() {
    const user_id = document.getElementById("user_id").value;
    const firstname = document.getElementById("firstname").value;
    const lastname = document.getElementById("lastname").value;
    const username = document.getElementById("username").value;
    const password = document.getElementById("password").value;
    const department = document.getElementById("department").value;
    const role = document.getElementById("role").value;
    const picture = document.getElementById("picture").files[0];

    let cards = [];
    document.querySelectorAll(".card-item").forEach(c => {
        cards.push({
            uid: c.querySelector(".card-uid").value,
            accessLevel: parseInt(c.querySelector(".card-access").value),
            flags: parseInt(c.querySelector(".card-flags").value),
            expiry: parseInt(c.querySelector(".card-expiry").value),
            authorized_local: c.querySelector(".card-auth").checked,
            timestamp: parseInt(c.querySelector(".card-ts").value)
        });
    });

    const formData = new FormData();
    formData.append("firstname", firstname);
    formData.append("lastname", lastname);
    formData.append("username", username);
    formData.append("password", password);
    formData.append("department", department);
    formData.append("role", role);
    if (picture) formData.append("picture", picture);
    formData.append("cards", JSON.stringify(cards));
    if (user_id) formData.append("user_id", user_id);

    const url = user_id ? 'edit_user.php' : 'add_user.php';

    fetch(url, { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            document.getElementById("formMessage").innerText = data.message;
            if(data.status === "success") setTimeout(() => location.reload(), 1000);
        });
}

// ----------------- EDIT USER -----------------
function editUser(btn) {
    const tr = btn.closest("tr");
    const user = JSON.parse(tr.dataset.user);
    const cards = JSON.parse(tr.dataset.cards);

    document.getElementById("user_id").value = user.id;
    document.getElementById("firstname").value = user.firstname;
    document.getElementById("lastname").value = user.lastname;
    document.getElementById("username").value = user.username;
    document.getElementById("password").value = '';
    document.getElementById("department").value = user.department;
    document.getElementById("role").value = user.role;

    cardsContainer.innerHTML = '';
    cards.forEach(uid => addCard(uid));
    document.getElementById("formTitle").innerText = "Edit User";
}

// ----------------- DELETE USER -----------------
function deleteUser(user_id) {
    if(!confirm("Are you sure you want to delete this user?")) return;
    const formData = new FormData();
    formData.append("user_id", user_id);

    fetch("delete_user.php", { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            alert(data.message);
            if(data.status === "success") location.reload();
        });
}

// ----------------- SEARCH FILTER -----------------
document.addEventListener("DOMContentLoaded", () => {
    const input = document.getElementById("searchInput");
    input.addEventListener("keyup", () => {
        const filter = input.value.toLowerCase();
        document.querySelectorAll(".admin-table tbody tr").forEach(tr => {
            const name = tr.cells[1].innerText.toLowerCase();
            const dept = tr.cells[3].innerText.toLowerCase();
            tr.style.display = name.includes(filter) || dept.includes(filter) ? "" : "none";
        });
    });
});
