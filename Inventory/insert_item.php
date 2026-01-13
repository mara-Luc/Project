<?php
include 'db.php';

$name = $_POST['name'];
$category_id = $_POST['category_id'];
$quantity = $_POST['quantity'];
$unit = $_POST['unit'];

$stmt = $conn->prepare("INSERT INTO items (name, category_id, quantity, unit) VALUES (?, ?, ?, ?)");
$stmt->bind_param("sids", $name, $category_id, $quantity, $unit);

if ($stmt->execute()) {
    echo "Item added successfully.<br><a href='add_item.php'>Add another</a> | <a href='view_inventory.php'>View Inventory</a>";
} else {
    echo "Error: " . $stmt->error;
}
?>