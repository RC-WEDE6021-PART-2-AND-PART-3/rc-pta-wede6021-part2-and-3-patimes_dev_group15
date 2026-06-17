<?php
session_start();
include "DBConn.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$address = $_POST["address"] ?? "No address provided";

// 1. Insert the order
$stmt = $conn->prepare("INSERT INTO ORDERS (user_id, delivery_address) VALUES (?, ?)");
$stmt->bind_param("is", $user_id, $address);
$stmt->execute();

// 2. Get the cart ID
$cart = $conn->query("SELECT cart_id FROM CART WHERE user_id = $user_id")->fetch_assoc();
$cart_id = $cart["cart_id"];

// 3. Delete all items from the cart (Checkout complete!)
$conn->query("DELETE FROM CART_ITEMS WHERE cart_id = $cart_id");

// 4. Redirect to success page
header("Location: order-success.php");
exit();
?>