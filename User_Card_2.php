<?php

function renderUserCard($picture, $firstname, $lastname, $department, $status = "Active") {
    $imgSrc = $picture && strlen($picture) > 0
        ? "data:image/jpeg;base64," . base64_encode($picture)
        : "img/default_user.png";

    $fullname = htmlspecialchars("$firstname $lastname");
    $dept = htmlspecialchars($department);
    $statusClass = strtolower($status) === "offline" ? "badge offline" : "badge";

    return "
        <div class='user-card'>
            <div class='user-avatar'>
                <img src='{$imgSrc}' alt='{$fullname}'>
            </div>
            <div class='user-info'>
                <p class='name'>{$fullname}</p>
                <p class='department'>{$dept}</p>
                <span class='{$statusClass}'>{$status}</span>
            </div>
            <div class='user-actions'>
                <button class='icon-btn' title='View Profile'><i class='bx bx-user'></i></button>
                <button class='icon-btn' title='Send Message'><i class='bx bx-envelope'></i></button>
            </div>
        </div>
    ";
}
?>