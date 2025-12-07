<?php
session_start();

// Include required files
include 'db_connect.php';
include 'User_Card.php';

// Access control
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    echo "Access denied. You must be an Admin to view this page.";
    exit;
}

// Query user data
$sql = "SELECT picture, firstname, lastname, department FROM users";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring Center</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h1>Monitoring Center</h1>
    <div class="user-grid">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo renderUserCard(
                    $row['picture'],
                    $row['firstname'],
                    $row['lastname'],
                    $row['department']
                );
            }
        } else {
            echo "<p>No users found in the database.</p>";
        }
        $conn->close();
        ?>
    </div>
</div>

<!-- JavaScript: Popup alert -->
<script>
function checkSecurityAlert() {
    fetch("get_alert.php")
        .then(response => response.text())
        .then(data => {
            if (data) showPopup(data);
        });
}

function showPopup(message) {
    let popup = document.createElement("div");
    popup.innerHTML = `
        <div style="
            position: fixed; top: 20%; left: 50%; transform: translate(-50%, -50%);
            background: red; color: white; padding: 20px; border-radius: 10px;
            font-size: 18px; box-shadow: 0px 0px 10px black; text-align: center;">
            <strong>${message}</strong>
        </div>`;

    document.body.appendChild(popup);
    setTimeout(() => popup.remove(), 5000);
}

setInterval(checkSecurityAlert, 3000);
</script>

</body>
</html>
