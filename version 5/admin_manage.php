<?php
session_start();
require_once 'db_connect.php';
require_once 'User_Card.php';

/* =====================
   ADMIN ACCESS CHECK
   ===================== */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: index.php");
    exit;
}

/* =====================
   PAGINATION SETUP
   ===================== */
$limit = 10; // users per page
$page  = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $limit;

/* =====================
   BASE QUERY
   ===================== */
$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM users ORDER BY id DESC LIMIT ?, ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $offset, $limit);
$stmt->execute();
$result = $stmt->get_result();

/* Get total rows */
$total = $conn->query("SELECT FOUND_ROWS()")->fetch_row()[0];
$total_pages = ceil($total / $limit);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>User Management</title>
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="admin_glass.css">
</head>
<body>

<div class="wrapper">

<!-- NAV -->
<nav class="nav">
  <div class="nav-logo"><p>Portalz.</p></div>
</nav>

<div class="admin-panel">

<!-- HEADER -->
<div class="glass-panel panel-header">
  <div>
    <h1>User Management</h1>
    <p>Identity & access control console</p>
  </div>

  <div class="panel-actions">
    <input id="searchInput" placeholder="Search users...">
    <select id="roleFilter">
      <option value="">All Roles</option>
      <option>Admin</option>
      <option>User</option>
    </select>
    <button onclick="openAddUser()">+ Add User</button>
  </div>
</div>

<!-- USER TABLE -->
<div class="glass-panel">
  <table class="neon-table">
    <thead>
      <tr>
        <th></th>
        <th>Name</th>
        <th>Username</th>
        <th>Department</th>
        <th>Status</th>
        <th>Role</th>
        <th></th>
      </tr>
    </thead>
    <tbody id="userTable">
      <?php while ($row = $result->fetch_assoc()) {
          echo renderUserRow($row);
      } ?>
    </tbody>
  </table>
</div>

<!-- PAGINATION -->
<div class="pagination">
<?php for ($i=1;$i<=$total_pages;$i++): ?>
  <a href="?page=<?= $i ?>" class="<?= $i==$page?'active':'' ?>">
    <?= $i ?>
  </a>
<?php endfor; ?>
</div>

</div>
</div>

<?php include 'admin_ui_components.php'; ?>
<script src="admin.js"></script>
</body>
</html>
