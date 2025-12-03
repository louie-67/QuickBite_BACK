<?php
header("Content-Type: application/json");
session_start();
require "../config.php";

$user_id = $_SESSION['user_id'] ?? 1;

$query = $conn->prepare("
    SELECT 
        c.id AS cart_id,
        m.id AS menu_item_id,
        m.name,
        m.price,
        c.quantity
    FROM cart c
    JOIN menu_items m ON c.menu_item_id = m.id
    WHERE c.user_id=?
");
$query->bind_param("i", $user_id);
query->execute();
$result = $query->get_result();

$items = [];
while ($row = $result->fetch_assoc()) {
    $items[] = $row;
}

echo json_encode($items);
?>
