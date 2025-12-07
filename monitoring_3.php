<?php
session_start();

include 'db_connect.php';
include 'components.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    echo "Access denied. Admins only.";
    exit;
}

$sql = "SELECT picture, firstname, lastname, department FROM users";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Monitoring Center</title>
    <link rel="stylesheet" href="style.css">
    <!---->
</head>
<body>

<div class="container">
    <h1>Monitoring Center</h1>

    <!-- Optional Search Bar -->
    <div class="search-box">
        <input type="text" id="searchInput" placeholder="Search by name or department...">
    </div>

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
            echo "<p>No users found.</p>";
        }

        $conn->close();
    ?>
    </div>
</div>

<script src="script.js?v=<?php echo time(); ?>"></script>
</body>
</html>

