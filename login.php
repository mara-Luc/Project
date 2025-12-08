<?php
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $user['role'];

            if ($user['role'] === 'Admin') {
                header("Location: monitoring.php");
                exit;
            } else {
                header("Location: login.php?error=access_denied");
                exit;
            }
        } else {
            header("Location: login.php?error=invalid_password");
            exit;
        }
    } else {
        header("Location: login.php?error=user_not_found");
        exit;
    }
}
?>