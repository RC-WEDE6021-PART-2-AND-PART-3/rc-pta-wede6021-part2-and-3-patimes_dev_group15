<?php
// Starts session to track logged-in user.
session_start();

// Enables error reporting for debugging.
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Connects to the database.
include "DBConn.php";

// Checks if user is logged in AND is an admin.
// 
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "admin") {
    header("Location: login.php");
    exit();
}


   //ADD USER FUNCTION
  
// 
if (isset($_POST["addUser"])) {

    // Gets form values
    $name = $_POST["name"];
    $email = $_POST["email"];
    $username = $_POST["username"];

    // Hashes password before storing
    // 
    $password = md5($_POST["password"]);

    $role = $_POST["role"];
    $status = $_POST["status"];

    // Inserts new user into USERS table
    $stmt = $conn->prepare("INSERT INTO USERS (name, email, username, password, role, status) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $name, $email, $username, $password, $role, $status);
    $stmt->execute();

    // Reloads page to show updated data
    header("Location: admin.php");
    exit();
}


   //UPDATE USER FUNCTION
  
// 
if (isset($_POST["updateUser"])) {

    $id = $_POST["id"];
    $name = $_POST["name"];
    $email = $_POST["email"];
    $username = $_POST["username"];
    $role = $_POST["role"];
    $status = $_POST["status"];

    // Updates user record in database
    $stmt = $conn->prepare("UPDATE USERS SET name=?, email=?, username=?, role=?, status=? WHERE user_id=?");
    $stmt->bind_param("sssssi", $name, $email, $username, $role, $status, $id);
    $stmt->execute();

    header("Location: admin.php");
    exit();
}


  // DELETE USER FUNCTION
  
// 
if (isset($_GET["delete"])) {

    $id = $_GET["delete"];

    // Prevents admin account from being deleted
    $stmt = $conn->prepare("DELETE FROM USERS WHERE user_id=? AND role != 'admin'");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: admin.php");
    exit();
}


   // APPROVE USER FUNCTION
  
// 
if (isset($_GET["approve"])) {

    $id = $_GET["approve"];

    // Changes status to approved
    $stmt = $conn->prepare("UPDATE USERS SET status='approved' WHERE user_id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: admin.php");
    exit();
}

// Fetches all users except admin for display
// 
$result = $conn->query("SELECT * FROM USERS WHERE role != 'admin'");
?>