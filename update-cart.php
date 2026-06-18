<?php
session_start();
include "DBConn.php";
if (!isset($_SESSION["user_id"])) { header("Location: login.php"); exit(); }

$id = $_GET["id"];
$action = $_GET["action"];

// Get current quantity
$q = $conn->query("SELECT quantity FROM CART_ITEMS WHERE cart_item_id = $id")->fetch_assoc();
$qty = $q['quantity'];

if ($action == "increase") {
    $conn->query("UPDATE CART_ITEMS SET quantity = quantity + 1 WHERE cart_item_id = $id");
} elseif ($action == "decrease" && $qty > 1) {
    $conn->query("UPDATE CART_ITEMS SET quantity = quantity - 1 WHERE cart_item_id = $id");
}

header("Location: cart.php");
exit();
?>