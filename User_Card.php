<?php
/* =====================
   USER ROW RENDERER
   ===================== */
function renderUserRow($row) {
    $id = (int)$row['id'];
    $name = htmlspecialchars($row['firstname'].' '.$row['lastname']);
    $username = htmlspecialchars($row['username']);
    $dept = htmlspecialchars($row['department']);
    $role = htmlspecialchars($row['role']);
    $status = htmlspecialchars($row['status']);

    $picture = !empty($row['picture'])
        ? "data:image/jpeg;base64,".base64_encode($row['picture'])
        : "https://via.placeholder.com/40";

    return "
    <tr onclick=\"openProfile($id)\">
      <td><img src='$picture'></td>
      <td>$name</td>
      <td>$username</td>
      <td>$dept</td>
      <td><span class='status $status'>$status</span></td>
      <td>$role</td>
      <td>
        <button onclick=\"event.stopPropagation();openEdit($id)\">✏️</button>
      </td>
    </tr>";
}
