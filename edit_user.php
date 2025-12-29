<?php
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $user_id = (int)$_POST['user_id'];
    $firstname = htmlspecialchars($_POST['firstname']);
    $lastname = htmlspecialchars($_POST['lastname']);
    $username = htmlspecialchars($_POST['username']);
    $department = htmlspecialchars($_POST['department']);
    $role = $_POST['role'];

    // optional password update
    $password_sql = '';
    $password_param = null;
    if (!empty($_POST['password'])) {
        $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $password_sql = ", password=?";
        $password_param = $password_hash;
    }

    // optional picture update
    $picture_sql = '';
    $picture_param = null;
    if (isset($_FILES['picture']) && $_FILES['picture']['error'] == 0) {
        $picture = file_get_contents($_FILES['picture']['tmp_name']);
        $picture_sql = ", picture=?";
        $picture_param = $picture;
    }

    // build dynamic SQL
    $params = [$firstname, $lastname, $username, $department, $role];
    $types = "sssss";

    if ($password_param) { $types .= "s"; $params[] = $password_param; }
    if ($picture_param) { $types .= "s"; $params[] = $picture_param; }

    $params[] = $user_id;
    $types .= "i";

    $sql = "UPDATE users SET firstname=?, lastname=?, username=?, department=?, role=? $password_sql $picture_sql WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $stmt->close();

    // update cards
    if (!empty($_POST['cards'])) {
        $cards = json_decode($_POST['cards'], true);

        // delete old cards
        $conn->query("DELETE FROM user_cards WHERE user_id=$user_id");

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

    echo json_encode(['status'=>'success','message'=>'User updated successfully']);
    $conn->close();
}
?>
