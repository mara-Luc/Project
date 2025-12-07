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

    <style>
        /* Basic page styling */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
            color: #333;
        }
        .container {
            width: 80%;
            margin: 20px auto;
        }
        h1 {
            text-align: center;
            margin-bottom: 30px;
        }
        .user-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }
        .user-card {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            width: 200px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }
        .user-card img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 10px;
        }
        .user-card p {
            margin: 5px 0;
            font-size: 16px;
        }
        .user-card .department {
            color: #777;
            font-size: 14px;
        }
    </style>
    
    <div class="wrapper">
        <!-- Navigation bar (same as login) -->
        <nav class="nav">
            <div class="nav-logo">
                <p>Portalz.</p>
            </div>
            <div class="nav-menu" id="navMenu">
                <ul>
                    <li><a href="index.php" class="link">Login</a></li>
                    <li><a href="monitoring_2.php" class="link active">Monitoring Center</a></li>
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
            <?php
            // Display user data in a grid format
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
            // Close the database connection
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