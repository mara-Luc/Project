<?php include 'db.php'; ?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Inventory Item</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h2>Add Item to Inventory</h2>

<form action="insert_item.php" method="POST">
    <label>Item Name:</label>
    <input type="text" name="name" required>

    <label>Category:</label>
    <select name="category_id" required>
        <?php
        $result = $conn->query("SELECT * FROM categories ORDER BY name");
        while ($row = $result->fetch_assoc()) {
            echo "<option value='{$row['id']}'>{$row['name']}</option>";
        }
        ?>
    </select>

    <label>Quantity:</label>
    <input type="number" step="0.01" name="quantity" required>

    <label>Unit (kg, L, pcs, etc):</label>
    <input type="text" name="unit" required>

    <button type="submit">Add Item</button>
</form>

</body>
</html>