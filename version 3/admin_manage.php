<?php
session_start();
include 'db_connect.php';
include 'User_Card.php';

// Restrict access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    echo "<div class='error-message'>Access denied. Admins only.</div>";
    header("Location: index.php");
    exit;
}

// Add user
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_user'])) {
    $username   = htmlspecialchars($_POST["username"]);
    $password   = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $firstname  = htmlspecialchars($_POST["firstname"]);
    $lastname   = htmlspecialchars($_POST["lastname"]);
    $department = htmlspecialchars($_POST["department"]);
    $pin        = htmlspecialchars($_POST["pin"]);
    $RFID_UID   = htmlspecialchars($_POST["RFID_UID"]);
    $role       = ($_POST["role"] === "Admin") ? "Admin" : "User";

    $picture = (isset($_FILES["picture"]) && $_FILES["picture"]["error"] == 0) 
        ? file_get_contents($_FILES["picture"]["tmp_name"]) 
        : null;

    $sql = "INSERT INTO users (picture, username, password, firstname, lastname, department, pin, RFID_UID, role) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssss", $picture, $username, $password, $firstname, $lastname, $department, $pin, $RFID_UID, $role);

    echo $stmt->execute() 
        ? "<div class='success-message'>User added successfully.</div>" 
        : "<div class='error-message'>Error adding user: " . $stmt->error . "</div>";
    $stmt->close();
}

// Delete user
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete_user'])) {
    $user_id = intval($_POST["user_id"]);
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);

    echo $stmt->execute() 
        ? "<div class='success-message'>User deleted successfully.</div>" 
        : "<div class='error-message'>Error deleting user: " . $stmt->error . "</div>";
    $stmt->close();
}

// Retrieve users
$sql = "SELECT id, firstname, lastname, username, department, pin, RFID_UID, role, picture FROM users";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Management</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="wrapper">
        <nav class="nav">
            <div class="nav-logo"><p>Portalz.</p></div>
            <div class="nav-menu" id="navMenu">
                <ul>
                    <li><a href="index.php" class="link">Login</a></li>
                    <li><a href="monitoring.php" class="link">Monitoring Center</a></li>
                    <li><a href="admin_manage.php" class="link active">Control Center</a></li>
                    <li><a href="history.php" class="link">History</a></li>
                    <li><a href="logs.php" class="link">Logs</a></li>
                </ul>
            </div>
        </nav>

        <div class="admin-container">
            <h1>Admin Management Page</h1>

            <h2>Add User</h2>
            <form method="POST" action="" enctype="multipart/form-data" class="admin-form">
                <label>First Name:</label>
                <input type="text" name="firstname" required>
                <label>Last Name:</label>
                <input type="text" name="lastname" required>
                <label>Username:</label>
                <input type="text" name="username" required>
                <label>Password:</label>
                <input type="password" name="password" required>
                <label>Department:</label>
                <input type="text" name="department" required>
                <label>PIN:</label>
                <input type="text" name="pin" maxlength="4" pattern="\d{4}" title="Enter a 4-digit PIN" required>
                <label>RFID UID:</label>
                <input type="text" name="RFID_UID" maxlength="8" required>
                <label>Role:</label>
                <select name="role">
                    <option value="User">User</option>
                    <option value="Admin">Admin</option>
                </select>
                <label>Profile Picture:</label>
                <input type="file" name="picture" accept="image/*">
                <button type="submit" name="add_user">Add User</button>
            </form>

            <h2>User List</h2>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Picture</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Username</th>
                        <th>Department</th>
                        <th>PIN</th>
                        <th>RFID UID</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo renderUserRow($row); // helper function
                        }
                    } else {
                        echo "<tr><td colspan='10'>No users found in the database.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>