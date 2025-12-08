<?php
// Render a user card (used in Monitoring Center)
function renderUserCard($row) {
    $firstname = htmlspecialchars($row['firstname']);
    $lastname  = htmlspecialchars($row['lastname']);
    $department = htmlspecialchars($row['department']);
    $picture = !empty($row['picture']) 
        ? "data:image/jpeg;base64," . base64_encode($row['picture']) 
        : "https://via.placeholder.com/100";

    return "
        <div class='user-card'>
            <img src='{$picture}' alt='User Picture'>
            <p>{$firstname} {$lastname}</p>
            <p class='department'>{$department}</p>
        </div>
    ";
}

// Render a user row (used in Admin Management table)
function renderUserRow($row) {
    $id        = htmlspecialchars($row['id']);
    $firstname = htmlspecialchars($row['firstname']);
    $lastname  = htmlspecialchars($row['lastname']);
    $username  = htmlspecialchars($row['username']);
    $department= htmlspecialchars($row['department']);
    $pin       = htmlspecialchars($row['pin']);
    $RFID_UID  = htmlspecialchars($row['RFID_UID']);
    $role      = htmlspecialchars($row['role']);
    $picture   = !empty($row['picture']) 
        ? "data:image/jpeg;base64," . base64_encode($row['picture']) 
        : "https://via.placeholder.com/50";

    return "
        <tr>
            <td>{$id}</td>
            <td><img src='{$picture}' alt='Profile Picture'></td>
            <td>{$firstname}</td>
            <td>{$lastname}</td>
            <td>{$username}</td>
            <td>{$department}</td>
            <td>{$pin}</td>
            <td>{$RFID_UID}</td>
            <td>{$role}</td>
            <td>
                <form method='POST' action='' style='display:inline-block;'>
                    <input type='hidden' name='user_id' value='{$id}'>
                    <button type='submit' name='delete_user' class='btn-danger'>Delete</button>
                </form>
            </td>
        </tr>
    ";
}
?>
