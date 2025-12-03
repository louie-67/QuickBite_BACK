<?php
header("Content-Type: application/json");
session_start();
require "../config.php";

$user_id = $_SESSION['user_id'] ?? 1;
$address = $_POST["address"] ?? "Default Address";

// 1. Load cart items
$cart = $conn->prepare("
    SELECT c.menu_item_id, c.quantity, m.price
    FROM cart c
    JOIN menu_items m ON c.menu_item_id = m.id
    WHERE c.user_id=?
");
$cart->bind_param("i", $user_id);
$cart->execute();
$result = $cart->get_result();

if ($result->num_rows == 0) {
    echo json_encode(["success" => false, "message" => "Cart is empty"]);
    exit;
}

$items = [];
$total = 0;

while ($row = $result->fetch_assoc()) {
    $row_total = $row["price"] * $row["quantity"];
    $total += $row_total;
    $items[] = $row;
}

// 2. Create order
$order = $conn->prepare("
    INSERT INTO orders (user_id, total_amount, delivery_address)
    VALUES (?, ?, ?)
");
$order->bind_param("ids", $user_id, $total, $address);
$order->execute();

$order_id = $conn->insert_id;

// 3. Insert items into order_items
foreach ($items as $i) {
    $add = $conn->prepare("
        INSERT INTO order_items (order_id, menu_item_id, quantity, price_at_time)
        VALUES (?, ?, ?, ?)
    ");
    $add->bind_param("iiid", $order_id, $i["menu_item_id"], $i["quantity"], $i["price"]);
    $add->execute();
}

// 4. Clear cart
$clear = $conn->prepare("DELETE FROM cart WHERE user_id=?");
$clear->bind_param("i", $user_id);
$clear->execute();

echo json_encode(["success" => true, "order_id" => $order_id]);
?>
