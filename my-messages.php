<?php
session_start();
include "DBConn.php";

// Only logged in users can see this
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

// Mark all unread messages as read when the user visits this page
$conn->query("UPDATE MESSAGES SET is_read = 1 WHERE receiver_id = $user_id");

// Fetch messages sent to this user
$messages = $conn->query("
    SELECT m.*, 
           u.name as sender_name, 
           i.title as item_title 
    FROM MESSAGES m
    JOIN USERS u ON m.sender_id = u.user_id
    LEFT JOIN ITEMS i ON m.item_id = i.item_id
    WHERE m.receiver_id = $user_id
    ORDER BY m.date_sent DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Messages | Pastimes</title>
    <link rel="stylesheet" href="css/styling.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .msg-box { background: white; border: 1px solid #ddd; padding: 20px; margin-bottom: 15px; border-radius: 6px; }
        .msg-header { display: flex; justify-content: space-between; margin-bottom: 8px; }
        .msg-sender { font-weight: bold; color: #2f6b57; }
        .msg-date { font-size: 13px; color: #888; }
    </style>
</head>
<body>
<nav class="navbar">
    <div class="logo">Pastimes</div>
    <div class="nav-links">
        <a href="index.php">Home</a>
        <a href="shop.php">Shop</a>
        <a href="about.php">About</a>
        <a href="contact.php">Contact</a>
        <div class="nav-icons">
            <a href="cart.php" class="bag-icon"><i class="fa-solid fa-bag-shopping"></i></a>
            <a href="logout.php" class="user-icon"><i class="fa-solid fa-right-from-bracket"></i></a>
        </div>
    </div>
</nav>

<div style="padding: 60px 24%; min-height: 80vh; background: #f8f6f2;">
    <h1 style="font-family: Georgia, serif; font-weight: 400;">My Messages</h1>
    <p style="color: #777; margin-bottom: 30px;">View messages from sellers and the admin.</p>

    <?php if($messages->num_rows == 0) { ?>
        <div style="background: white; border: 1px solid #ddd; padding: 40px; text-align: center; color: #777;">
            <p>You don't have any messages yet.</p>
        </div>
    <?php } else { ?>
        <?php while($msg = $messages->fetch_assoc()) { ?>
            <div class="msg-box">
                <div class="msg-header">
                    <span class="msg-sender">From: <?php echo htmlspecialchars($msg['sender_name']); ?></span>
                    <span class="msg-date"><?php echo date("M j, g:i a", strtotime($msg['date_sent'])); ?></span>
                </div>
                <p style="font-weight: bold; color: #333;"><?php echo htmlspecialchars($msg['subject']); ?></p>
                <p style="color: #555;"><?php echo nl2br(htmlspecialchars($msg['message'])); ?></p>
                <?php if($msg['item_title']) { ?>
                    <p style="font-size: 13px; color: #2f6b57; margin-top: 10px;">📦 Regarding: <?php echo htmlspecialchars($msg['item_title']); ?></p>
                <?php } ?>
            </div>
        <?php } ?>
    <?php } ?>
</div>
</body>
</html>