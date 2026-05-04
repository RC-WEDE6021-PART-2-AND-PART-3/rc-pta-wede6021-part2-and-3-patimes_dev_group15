<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "DBConn.php";

$conn->query("DROP TABLE IF EXISTS CART_ITEMS");
$conn->query("DROP TABLE IF EXISTS CART");
$conn->query("DROP TABLE IF EXISTS ORDERS");
$conn->query("DROP TABLE IF EXISTS ITEMS");
$conn->query("DROP TABLE IF EXISTS USERS");

$conn->query("CREATE TABLE USERS (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    username VARCHAR(50),
    password VARCHAR(255),
    role VARCHAR(20) DEFAULT 'customer',
    status VARCHAR(20) DEFAULT 'pending'
)");

$conn->query("CREATE TABLE ITEMS (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    title VARCHAR(100),
    description TEXT,
    price DECIMAL(10,2),
    image VARCHAR(255),
    FOREIGN KEY (user_id) REFERENCES USERS(user_id)
)");

$conn->query("CREATE TABLE ORDERS (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    delivery_address TEXT,
    FOREIGN KEY (user_id) REFERENCES USERS(user_id)
)");

$conn->query("CREATE TABLE CART (
    cart_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNIQUE,
    FOREIGN KEY (user_id) REFERENCES USERS(user_id)
)");

$conn->query("CREATE TABLE CART_ITEMS (
    cart_item_id INT AUTO_INCREMENT PRIMARY KEY,
    cart_id INT,
    item_id INT,
    quantity INT DEFAULT 1,
    FOREIGN KEY (cart_id) REFERENCES CART(cart_id),
    FOREIGN KEY (item_id) REFERENCES ITEMS(item_id)
)");

echo "Tables created successfully.<br>";

// LOAD USERS
$file = fopen("data/userData.txt", "r");
while (($line = fgetcsv($file)) !== FALSE) {
    $stmt = $conn->prepare("INSERT INTO USERS(name,email,username,password,role,status) VALUES(?,?,?,?,?,?)");
    $stmt->bind_param("ssssss", $line[0], $line[1], $line[2], $line[3], $line[4], $line[5]);
    $stmt->execute();
}
fclose($file);
echo "Users loaded.<br>";

// LOAD ITEMS
$file = fopen("data/itemsData.txt", "r");
while (($line = fgetcsv($file)) !== FALSE) {
    $stmt = $conn->prepare("INSERT INTO ITEMS(user_id,title,description,price,image) VALUES(?,?,?,?,?)");
    $stmt->bind_param("issds", $line[0], $line[1], $line[2], $line[3], $line[4]);
    $stmt->execute();
}
fclose($file);
echo "Items loaded.<br>";

// LOAD ORDERS
$file = fopen("data/ordersData.txt", "r");
while (($line = fgetcsv($file)) !== FALSE) {
    $stmt = $conn->prepare("INSERT INTO ORDERS(user_id,delivery_address) VALUES(?,?)");
    $stmt->bind_param("is", $line[0], $line[1]);
    $stmt->execute();
}
fclose($file);
echo "Orders loaded.<br>";

// LOAD CART
$file = fopen("data/cartData.txt", "r");
while (($line = fgetcsv($file)) !== FALSE) {
    $stmt = $conn->prepare("INSERT INTO CART(user_id) VALUES(?)");
    $stmt->bind_param("i", $line[0]);
    $stmt->execute();
}
fclose($file);
echo "Carts loaded.<br>";

// LOAD CART ITEMS
$file = fopen("data/cartItemsData.txt", "r");
while (($line = fgetcsv($file)) !== FALSE) {
    $stmt = $conn->prepare("INSERT INTO CART_ITEMS(cart_id,item_id,quantity) VALUES(?,?,?)");
    $stmt->bind_param("iii", $line[0], $line[1], $line[2]);
    $stmt->execute();
}
fclose($file);
echo "Cart items loaded.<br>";

echo "<br>ClothingStore database loaded successfully.";
?>