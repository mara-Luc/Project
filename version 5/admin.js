const search = document.getElementById("searchInput");
const role   = document.getElementById("roleFilter");

search.onkeyup = role.onchange = () => {
  fetch(`admin_actions.php?action=search&q=${search.value}&role=${role.value}`)
    .then(r=>r.text())
    .then(html=>document.getElementById("userTable").innerHTML = html);
};

function openProfile(id) {
  // future expansion: load profile via AJAX
  alert("Profile view for user " + id);
}

function openEdit(id) {
  alert("Edit modal for user " + id);
}
