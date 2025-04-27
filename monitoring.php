<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    echo "Access denied.";
    exit;
}
include 'db_connect.php';

$sql = "SELECT firstname, lastname, profile_picture FROM users"; // Adjust column names
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Monitoring</title>
</head>
<body>
    <h2>Monitoring Center</h2>
    <div>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div>
                <img src="data:image/jpeg;base64,<?= base64_encode($row['profile_picture']) ?>" alt="User Picture" width="100" height="100">
                <p><?= htmlspecialchars($row['firstname'] . " " . $row['lastname']) ?></p>
            </div>
        <?php endwhile; ?>
    </div>
</body>
</html>