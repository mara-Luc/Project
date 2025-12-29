<?php
require 'db_connect.php';

/* =====================
   AJAX SEARCH
   ===================== */
if ($_GET['action'] === 'search') {
    $q = "%".$_GET['q']."%";
    $role = $_GET['role'];

    $sql = "SELECT * FROM users WHERE 
            (firstname LIKE ? OR lastname LIKE ? OR username LIKE ?)";
    if ($role) $sql .= " AND role = ?";

    $stmt = $conn->prepare($sql);
    if ($role) {
        $stmt->bind_param("ssss",$q,$q,$q,$role);
    } else {
        $stmt->bind_param("sss",$q,$q,$q);
    }

    $stmt->execute();
    $res = $stmt->get_result();

    require 'User_Card.php';
    while ($row = $res->fetch_assoc()) {
        echo renderUserRow($row);
    }
}

/* =====================
   UPDATE STATUS
   ===================== */
if ($_POST['action'] === 'status') {
    $stmt = $conn->prepare(
        "UPDATE users SET status=? WHERE id=?"
    );
    $stmt->bind_param("si",$_POST['status'],$_POST['id']);
    $stmt->execute();
}
