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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="style.css">
    <title>Portals | Monitoring Center</title>
</head>
<body>
    <div class="wrapper">
        <!-- Navigation bar (same as login) -->
        <nav class="nav">
            <div class="nav-logo">
                <p>Portals.</p>
            </div>
            <div class="nav-menu" id="navMenu">
                <ul>
                    <li><a href="login.php" class="link">Login</a></li>
                    <li><a href="monitoring.php" class="link active">Monitoring Center</a></li>
                    <li><a href="admin_manage.php" class="link">Control Center</a></li>
                    <li><a href="history.php" class="link">History</a></li>
                    <li><a href="logs.php" class="link">Logs</a></li>
                </ul>
            </div>
        </nav>

        <!-- Monitoring Center content -->
        <div class="form-box">
            <div class="monitoring-container">
                <div class="top">
                    <header>Monitoring Center</header>
                </div>
                <div class="user-grid">
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<div class='user-card'>";
                            echo "<img src='data:image/jpeg;base64," . base64_encode($row['picture']) . "' alt='User Picture'>";
                            echo "<p>" . htmlspecialchars($row['firstname']) . " " . htmlspecialchars($row['lastname']) . "</p>";
                            echo "<p class='department'>" . htmlspecialchars($row['department']) . "</p>";
                            echo "</div>";
                        }
                    } else {
                        echo "<p>No users found in the database.</p>";
                    }
                    $conn->close();
                    ?>
                </div>
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