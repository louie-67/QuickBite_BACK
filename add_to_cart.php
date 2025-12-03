<?php
header("Content-Type: application/json");
session_start();
require "../config.php";

$user_id = $_SESSION['user_id'] ?? 1; // change to session user

$data = json_decode(file_get_contents("php://input"), true);

$menu_item_id = $data["menu_item_id"];
$quantity = $data["quantity"];

// Check if already in cart
$check = $conn->prepare("
    SELECT id, quantity FROM cart 
    WHERE user_id=? AND menu_item_id=?
");
$check->bind_param("i i", $user_id, $menu_item_id);
$check->execute();
$res = $check->get_result();

if ($res->num_rows > 0) {
    // Update quantity
    $row = $res->fetch_assoc();
    $newQ = $row["quantity"] + $quantity;

    $update = $conn->prepare("UPDATE cart SET quantity=? WHERE id=?");
    $update->bind_param("i i", $newQ, $row["id"]);
    $update->execute();

} else {
    // Insert new cart item
    $insert = $conn->prepare("INSERT INTO cart (user_id, menu_item_id, quantity) VALUES (?,?,?)");
    $insert->bind_param("i i i", $user_id, $menu_item_id, $quantity);
    $insert->execute();
}

// cart count
$count = $conn->prepare("SELECT SUM(quantity) AS total FROM cart WHERE user_id=?");
$count->bind_param("i", $user_id);
$count->execute();
$total = $count->get_result()->fetch_assoc()["total"];

echo json_encode([
    "success" => true,
    "cart_count" => $total
]);
?>
