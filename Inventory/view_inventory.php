<?php include 'db.php'; ?>

<!DOCTYPE html>
<html>
<head>
    <title>Inventory</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h2>Current Inventory</h2>

<table>
    <tr>
        <th>Item</th>
        <th>Category</th>
        <th>Quantity</th>
        <th>Unit</th>
        <th>Last Updated</th>
    </tr>

<?php
$sql = "
SELECT items.*, categories.name AS category_name
FROM items
JOIN categories ON items.category_id = categories.id
ORDER BY categories.name, items.name
";

$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    echo "<tr>
            <td>{$row['name']}</td>
            <td>{$row['category_name']}</td>
            <td>{$row['quantity']}</td>
            <td>{$row['unit']}</td>
            <td>{$row['last_updated']}</td>
          </tr>";
}
?>
</table>

</body>
</html>