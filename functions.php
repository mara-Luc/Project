<?php
function renderUserCard($picture, $firstname, $lastname, $department) {
    $imgSrc = "data:image/jpeg;base64," . base64_encode($picture);
    $firstname = htmlspecialchars($firstname);
    $lastname = htmlspecialchars($lastname);
    $department = htmlspecialchars($department);

    return "
        <div class='user-card'>
            <img src='{$imgSrc}' alt='User Picture'>
            <p>{$firstname} {$lastname}</p>
            <p class='department'>{$department}</p>
        </div>
    ";
}
?>