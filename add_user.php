<?php
require 'db_connect.php'; // your DB connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // sanitize input
    $firstname = htmlspecialchars($_POST['firstname']);
    $lastname = htmlspecialchars($_POST['lastname']);
    $username = htmlspecialchars($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // secure hash
    $department = htmlspecialchars($_POST['department']);
    $role = $_POST['role'];
    
    // handle picture upload
    $picture = null;
    if (isset($_FILES['picture']) && $_FILES['picture']['error'] == 0) {
        $picture = file_get_contents($_FILES['picture']['tmp_name']);
    }

    // insert user
    $stmt = $conn->prepare("INSERT INTO users (firstname, lastname, username, password, department, role, picture) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $firstname, $lastname, $username, $password, $department, $role, $picture);
    
    if ($stmt->execute()) {
        $user_id = $stmt->insert_id;

        // handle card JSON array
        if (!empty($_POST['cards'])) {
            $cards = json_decode($_POST['cards'], true); // cards sent as JSON array
            $card_stmt = $conn->prepare("INSERT INTO user_cards (user_id, uid, access_level, flags, expiry, authorized_local, timestamp) VALUES (?, ?, ?, ?, ?, ?, ?)");
            
            foreach ($cards as $card) {
                $uid = $card['uid'];
                $access = $card['accessLevel'];
                $flags = $card['flags'];
                $expiry = $card['expiry'];
                $authorized = $card['authorized_local'] ? 1 : 0;
                $ts = $card['timestamp'];
                
                $card_stmt->bind_param("isiiiii", $user_id, $uid, $access, $flags, $expiry, $authorized, $ts);
                $card_stmt->execute();
            }
            $card_stmt->close();
        }

        echo json_encode(['status' => 'success', 'message' => 'User added successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to add user']);
    }

    $stmt->close();
    $conn->close();
}
?>
