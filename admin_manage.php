<?php
/*************************************************
 * ADMIN MANAGE PAGE
 * - Restricts access to Admins
 * - Handles search & filtering
 * - Handles add & delete user
 * - Displays glass-panel UI
 *************************************************/

session_start();
require_once 'db_connect.php';
require_once 'User_Card.php';

/* =============================
   ACCESS CONTROL
   ============================= */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: index.php");
    exit;
}

/* =============================
   ADD USER HANDLER (MODAL FORM)
   ============================= */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_user'])) {

    // Collect & sanitize input
    $firstname  = trim($_POST['firstname']);
    $lastname   = trim($_POST['lastname']);
    $username   = trim($_POST['username']);
    $password   = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $department = trim($_POST['department']);
    $pin        = trim($_POST['pin']);
    $RFID_UID   = trim($_POST['RFID_UID']);
    $role       = ($_POST['role'] === 'Admin') ? 'Admin' : 'User';

    // Handle profile picture (stored as BLOB – unchanged from your system)
    $picture = (!empty($_FILES['picture']['tmp_name']))
        ? file_get_contents($_FILES['picture']['tmp_name'])
        : null;

    // Insert user
    $stmt = $conn->prepare("
        INSERT INTO users 
        (picture, firstname, lastname, username, password, department, pin, RFID_UID, role)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param(
        "sssssssss",
        $picture,
        $firstname,
        $lastname,
        $username,
        $password,
        $department,
        $pin,
        $RFID_UID,
        $role
    );
    $stmt->execute();
    $stmt->close();

    header("Location: admin_manage.php");
    exit;
}

/* =============================
   DELETE USER HANDLER
   ============================= */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete_user'])) {
    $user_id = (int)$_POST['user_id'];

    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();

    header("Location: admin_manage.php");
    exit;
}

/* =============================
   SEARCH & FILTER LOGIC
   ============================= */
$where = [];
$params = [];
$types  = "";

// Text search
if (!empty($_GET['search'])) {
    $where[] = "(firstname LIKE ? OR lastname LIKE ? OR username LIKE ?)";
    $search = "%" . $_GET['search'] . "%";
    $params[] = $search;
    $params[] = $search;
    $params[] = $search;
    $types .= "sss";
}

// Role filter
if (!empty($_GET['role'])) {
    $where[] = "role = ?";
    $params[] = $_GET['role'];
    $types .= "s";
}

// Department filter
if (!empty($_GET['department'])) {
    $where[] = "department = ?";
    $params[] = $_GET['department'];
    $types .= "s";
}

// Build final SQL
$sql = "SELECT * FROM users";
if ($where) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>User Management</title>

<!-- YOUR EXISTING CSS -->
<link rel="stylesheet" href="style.css">

<!-- NEW GLASS + NEON LAYER -->
<link rel="stylesheet" href="admin_glass.css">
</head>
<body>

<div class="wrapper">

<!-- NAVIGATION (UNCHANGED) -->
<nav class="nav">
    <div class="nav-logo"><p>Portalz.</p></div>
    <div class="nav-menu">
        <ul>
            <li><a href="index.php" class="link">Login</a></li>
            <li><a href="monitoring.php" class="link">Monitoring Center</a></li>
            <li><a href="admin_manage.php" class="link active">Control Center</a></li>
            <li><a href="history.php" class="link">History</a></li>
            <li><a href="logs.php" class="link">Logs</a></li>
        </ul>
    </div>
</nav>

<!-- MAIN ADMIN PANEL -->
<div class="admin-panel">

    <!-- HEADER PANEL -->
    <div class="glass-panel panel-header">
        <div>
            <h1>User Management</h1>
            <p>Manage users, access, and credentials</p>
        </div>

        <!-- SEARCH + FILTERS -->
        <form method="GET" class="filter-form">
            <input type="text" name="search" placeholder="Search..."
                   value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">

            <select name="role">
                <option value="">All Roles</option>
                <option value="Admin">Admin</option>
                <option value="User">User</option>
            </select>

            <select name="department">
                <option value="">All Departments</option>
                <option value="IT">IT</option>
                <option value="Sales">Sales</option>
                <option value="Human Resources">Human Resources</option>
            </select>

            <button type="submit">Filter</button>
        </form>

        <button class="btn-primary" onclick="openAddUser()">+ Add User</button>
    </div>

    <!-- USER TABLE -->
    <div class="glass-panel">
        <table class="neon-table">
            <thead>
                <tr>
                    <th>Profile</th>
                    <th>Name</th>
                    <th>Username</th>
                    <th>Department</th>
                    <th>RFID</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) {
                    echo renderUserRow($row);
                } ?>
            </tbody>
        </table>
    </div>
</div>
</div>

<!-- ADD USER MODAL -->
<div id="addUserModal" class="modal">
    <div class="modal-content glass-panel">
        <h2>Add User</h2>
        <form method="POST" enctype="multipart/form-data">
            <input name="firstname" placeholder="First Name" required>
            <input name="lastname" placeholder="Last Name" required>
            <input name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <input name="department" placeholder="Department" required>
            <input name="pin" maxlength="4" placeholder="PIN" required>
            <input name="RFID_UID" placeholder="RFID UID" required>

            <select name="role">
                <option>User</option>
                <option>Admin</option>
            </select>

            <input type="file" name="picture">

            <button class="btn-primary" name="add_user">Create User</button>
            <button type="button" onclick="closeAddUser()">Cancel</button>
        </form>
    </div>
</div>

<script>
function openAddUser() {
    document.getElementById('addUserModal').style.display = 'block';
}
function closeAddUser() {
    document.getElementById('addUserModal').style.display = 'none';
}
</script>

</body>
</html>
