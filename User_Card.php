<?php
/*************************************************
 * USER RENDER HELPERS
 * - renderUserCard(): Monitoring Center cards
 * - renderUserRow(): Admin table rows
 *************************************************/

function renderUserCard($row) {
    $name = htmlspecialchars($row['firstname'] . ' ' . $row['lastname']);
    $department = htmlspecialchars($row['department']);

    $picture = !empty($row['picture'])
        ? "data:image/jpeg;base64," . base64_encode($row['picture'])
        : "https://via.placeholder.com/100";

    return "
    <div class='user-card'>
        <img src='{$picture}'>
        <p>{$name}</p>
        <p class='department'>{$department}</p>
    </div>";
}

function renderUserRow($row) {
    $id = (int)$row['id'];
    $name = htmlspecialchars($row['firstname'] . ' ' . $row['lastname']);
    $username = htmlspecialchars($row['username']);
    $department = htmlspecialchars($row['department']);
    $rfid = htmlspecialchars($row['RFID_UID']);
    $role = htmlspecialchars($row['role']);

    $picture = !empty($row['picture'])
        ? "data:image/jpeg;base64," . base64_encode($row['picture'])
        : "https://via.placeholder.com/50";

    return "
    <tr>
        <td><img src='{$picture}'></td>
        <td>{$name}</td>
        <td>{$username}</td>
        <td>{$department}</td>
        <td>{$rfid}</td>
        <td>{$role}</td>
        <td>
            <form method='POST' style='display:inline'>
                <input type='hidden' name='user_id' value='{$id}'>
                <button name='delete_user' class='icon-btn delete'>🗑</button>
            </form>
        </td>
    </tr>";
}
