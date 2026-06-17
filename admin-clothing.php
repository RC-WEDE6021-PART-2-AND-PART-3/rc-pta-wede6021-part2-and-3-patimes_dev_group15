<?php
session_start();
include "DBConn.php";
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "admin") { header("Location: login.php"); exit(); }

// ADD ITEM
if (isset($_POST["addItem"])) {
    $stmt = $conn->prepare("INSERT INTO ITEMS (user_id, title, description, price, image) VALUES (1, ?, ?, ?, ?)");
    $stmt->bind_param("ssds", $_POST["title"], $_POST["description"], $_POST["price"], $_POST["image"]);
    $stmt->execute();
    header("Location: admin.php");
    exit();
}

// DELETE ITEM
if (isset($_GET["delete"])) {
    $conn->query("DELETE FROM ITEMS WHERE item_id = " . $_GET["delete"]);
    header("Location: admin.php");
    exit();
}
?>