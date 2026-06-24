<?php
include "DBConn.php";

$conn->query("DROP TABLE IF EXISTS CART_ITEMS");
$conn->query("DROP TABLE IF EXISTS CART");
$conn->query("DROP TABLE IF EXISTS ORDERS");
$conn->query("DROP TABLE IF EXISTS ITEMS");
$conn->query("DROP TABLE IF EXISTS USERS");

$sql = "CREATE TABLE USERS (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    username VARCHAR(50),
    password VARCHAR(255),
    role VARCHAR(20) DEFAULT 'customer',
    status VARCHAR(20) DEFAULT 'pending'
)";

if ($conn->query($sql) === TRUE) {
    echo "USERS table created.<br>";
} else {
    die("Error creating USERS table: " . $conn->error);
}

$file = fopen("data/userData.txt", "r");

while (($line = fgetcsv($file)) !== FALSE) {
    $name = $line[0];
    $email = $line[1];
    $username = $line[2];
    $password = $line[3];
    $role = $line[4];
    $status = $line[5];

    $stmt = $conn->prepare("INSERT INTO USERS(name,email,username,password,role,status) VALUES(?,?,?,?,?,?)");
    $stmt->bind_param("ssssss", $name, $email, $username, $password, $role, $status);
    $stmt->execute();
}

fclose($file);

echo "User data loaded successfully.";
?>