<?php
require 'db_connect.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Manage Users</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container admin-container">

    <h1>Admin Management</h1>

    <!-- Add/Edit User Form -->
    <div class="admin-form">
        <h2 id="formTitle">Add User</h2>
        <input type="hidden" id="user_id" value="">
        <label>First Name</label>
        <input type="text" id="firstname" placeholder="First Name">

        <label>Last Name</label>
        <input type="text" id="lastname" placeholder="Last Name">

        <label>Username</label>
        <input type="text" id="username" placeholder="Username">

        <label>Password</label>
        <input type="password" id="password" placeholder="Password (leave blank to keep current)">

        <label>Department</label>
        <input type="text" id="department" placeholder="Department">

        <label>Role</label>
        <select id="role">
            <option value="Admin">Admin</option>
            <option value="User" selected>User</option>
        </select>

        <label>Picture</label>
        <input type="file" id="picture">

        <h3>RFID Cards</h3>
        <div id="cardsContainer"></div>
        <button type="button" onclick="addCard()">Add Card</button>

        <button type="button" id="submitBtn" onclick="submitForm()">Submit</button>
        <p id="formMessage"></p>
    </div>

    <!-- Search Users -->
    <input type="text" id="searchInput" placeholder="Search Users...">

    <!-- Users Table -->
    <table class="admin-table">
        <thead>
            <tr>
                <th>Picture</th>
                <th>Name</th>
                <th>Username</th>
                <th>Department</th>
                <th>Role</th>
                <th>Cards</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="usersTable">
            <?php
            $res = $conn->query("SELECT * FROM users");
            while ($row = $res->fetch_assoc()):
                $cardsRes = $conn->query("SELECT uid FROM user_cards WHERE user_id={$row['id']}");
                $cards = [];
                while($c = $cardsRes->fetch_assoc()){ $cards[] = $c['uid']; }
            ?>
            <tr data-user='<?php echo json_encode($row); ?>' data-cards='<?php echo json_encode($cards); ?>'>
                <td><img src="data:image/jpeg;base64,<?php echo base64_encode($row['picture']); ?>" alt="pic"></td>
                <td><?php echo $row['firstname']." ".$row['lastname']; ?></td>
                <td><?php echo $row['username']; ?></td>
                <td><?php echo $row['department']; ?></td>
                <td><?php echo $row['role']; ?></td>
                <td><?php echo implode(", ", $cards); ?></td>
                <td>
                    <button onclick="editUser(this)">Edit</button>
                    <button onclick="deleteUser(<?php echo $row['id']; ?>)">Delete</button>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script src="sandbox.js"></script>
</body>
</html>
