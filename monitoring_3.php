<?php
session_start();
include 'db_connect.php';

// Restrict access to Admin role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    echo "Access denied. You must be an Admin to view this page.";
    exit;
}

$sql = "SELECT picture, firstname, lastname, department FROM users";
$result = $conn->query($sql);

// Fetch all rows into an array
$users = [];
if ($result && $result->num_rows > 0) {
    $users = $result->fetch_all(MYSQLI_ASSOC);
}
$conn->close();

/**
 * Helper function to render a user card
 */
function renderUserCard(array $user): string {
    $picture = base64_encode($user['picture']);
    $firstname = htmlspecialchars($user['firstname']);
    $lastname = htmlspecialchars($user['lastname']);
    $department = htmlspecialchars($user['department']);

    return <<<HTML
        <div class="user-card">
            <img src="data:image/jpeg;base64,{$picture}" alt="User Picture">
            <p>{$firstname} {$lastname}</p>
            <p class="department">{$department}</p>
        </div>
    HTML;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <title>Portals | Monitoring Center</title>
</head>
<body>
    <div class="wrapper">
        <!-- Navigation bar -->
        <nav class="nav">
            <div class="nav-logo">
                <p>Portalz.</p>
            </div>
            <div class="nav-menu" id="navMenu">
                <ul>
                    <li><a href="index.php" class="link">Login</a></li>
                    <li><a href="monitoring_3.php" class="link active">Monitoring Center</a></li>
                    <li><a href="admin_manage.php" class="link">Control Center</a></li>
                    <li><a href="history.php" class="link">History</a></li>
                    <li><a href="logs.php" class="link">Logs</a></li>
                </ul>
            </div>
        </nav>

        <!-- Monitoring Center content -->
        <div class="container">
            <h1>Monitoring Center</h1>
            <div class="user-grid">
                <?php if (!empty($users)): ?>
                    <?php foreach ($users as $user): ?>
                        <?= renderUserCard($user) ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No users found in the database.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Security Alert Popup -->
    <script>
        function checkSecurityAlert() {
            fetch("get_alert.php")
                .then(response => response.text())
                .then(data => {
                    if (data) {
                        showPopup(data);
                    }
                });
        }

        function showPopup(message) {
            let popup = document.createElement("div");
            popup.className = "alert-popup";
            popup.innerHTML = `<strong>${message}</strong>`;
            document.body.appendChild(popup);
            setTimeout(() => { popup.remove(); }, 5000);
        }

        setInterval(checkSecurityAlert, 3000);
    </script>
</body>
</html>