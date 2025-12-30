<?php
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = (int)$_POST['user_id'];

    // delete user and cascade to cards
    $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
    $stmt->bind_param("i", $user_id);
    if ($stmt->execute()) {
        echo json_encode(['status'=>'success','message'=>'User deleted']);
    } else {
        echo json_encode(['status'=>'error','message'=>'Delete failed']);
    }
    $stmt->close();
    $conn->close();
}
?>
