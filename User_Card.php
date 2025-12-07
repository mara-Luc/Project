<?php
// Reusable function for user cards
function renderUserCard($picture, $firstname, $lastname, $department) {
    $imgSrc = "data:image/jpeg;base64," . base64_encode($picture);
    $fullname = htmlspecialchars($firstname . " " . $lastname);
    $dept = htmlspecialchars($department);

    return "
        <div class='user-card'>
            <img src='{$imgSrc}' alt='User Picture'>
            <p>{$fullname}</p>
            <p class='department'>{$dept}</p>
        </div>
    ";
}
?>