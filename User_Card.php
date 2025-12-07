<?php

// Renders a circular profile card for monitoring center & other pages
function renderUserCard($picture, $firstname, $lastname, $department) {

    // Fallback image if no picture exists
    if ($picture && strlen($picture) > 0) {
        $imgSrc = "data:image/jpeg;base64," . base64_encode($picture);
    } else {
        $imgSrc = "img/default_user.png"; // place a default icon here
    }

    $fullname = htmlspecialchars("$firstname $lastname");
    $dept = htmlspecialchars($department);

    return "
        <div class='user-card'>
            <img src='{$imgSrc}' alt='{$fullname}'>
            <p class='name'>{$fullname}</p>
            <p class='department'>{$dept}</p>
        </div>
    ";
}
?>
