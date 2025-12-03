<?php
require '../db.php';
header("Content-Type: application/json");

$query = $conn->query("SELECT * FROM menu_items WHERE is_available = 1 ORDER BY id DESC");

$menu = [];

while ($row = $query->fetch_assoc()) {
    $menu[] = [
        'id' => (int)$row['id'],
        'name' => $row['name'],
        'price' => (float)$row['price'],
        'desc' => $row['description'],
        'category' => $row['category']
    ];
}

echo json_encode($menu);
