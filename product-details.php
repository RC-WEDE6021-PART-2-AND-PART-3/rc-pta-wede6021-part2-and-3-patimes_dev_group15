<?php session_start(); include "DBConn.php"; $message = ""; $id = $_GET['id'] ?? 1; $stmt = $conn->prepare("SELECT * FROM ITEMS WHERE item_id = ?"); $stmt->bind_param("i", $id); $stmt->execute(); $result = $stmt->get_result(); if ($result->num_rows == 0) { die("Product not found."); } $product = $result->fetch_assoc(); if (isset($_POST['add_to_cart'])) { if (!isset($_SESSION["user_id"])) { $message = "Please login before adding items to cart."; } else { $user_id = $_SESSION["user_id"]; $cartCheck = $conn->prepare("SELECT cart_id FROM CART WHERE user_id = ?"); $cartCheck->bind_param("i", $user_id); $cartCheck->execute(); $cartResult = $cartCheck->get_result(); if ($cartResult->num_rows == 0) { $createCart = $conn->prepare("INSERT INTO CART (user_id) VALUES (?)"); $createCart->bind_param("i", $user_id); $createCart->execute(); $cart_id = $createCart->insert_id; } else { $cartRow = $cartResult->fetch_assoc(); $cart_id = $cartRow["cart_id"]; } $itemCheck = $conn->prepare("SELECT * FROM CART_ITEMS WHERE cart_id = ? AND item_id = ?"); $itemCheck->bind_param("ii", $cart_id, $id); $itemCheck->execute(); $itemResult = $itemCheck->get_result(); if ($itemResult->num_rows > 0) { $update = $conn->prepare("UPDATE CART_ITEMS SET quantity = quantity + 1 WHERE cart_id = ? AND item_id = ?"); $update->bind_param("ii", $cart_id, $id); $update->execute(); } else { $insert = $conn->prepare("INSERT INTO CART_ITEMS (cart_id, item_id, quantity) VALUES (?, ?, 1)"); $insert->bind_param("ii", $cart_id, $id); $insert->execute(); } $message = "Added to cart"; } } if (isset($_POST['send_message'])) { if (!isset($_SESSION["user_id"])) { $message = "Please login to message the seller."; } else { $sender_id = $_SESSION["user_id"]; $receiver_id = $product['user_id']; $item_id = $product['item_id']; $msg_subject = trim($_POST['subject']); $msg_text = trim($_POST['message_text']); $stmt = $conn->prepare("INSERT INTO MESSAGES (sender_id, receiver_id, item_id, subject, message) VALUES (?, ?, ?, ?, ?)"); $stmt->bind_param("iiiss", $sender_id, $receiver_id, $item_id, $msg_subject, $msg_text); if($stmt->execute()){ $message = "✅ Your message has been sent to the seller!"; } else { $message = "❌ Failed to send message."; } } } ?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title><?php echo htmlspecialchars($product['title']); ?> | Pastimes</title><link rel="stylesheet" href="css/styling.css"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"></head>
<body>
<nav class="navbar">
    <div class="logo">Pastimes</div>
    <div class="nav-links">
        <a href="index.php">Home</a><a href="shop.php">Shop</a><a href="about.php">About</a><a href="contact.php">Contact</a>
        <?php if(isset($_SESSION["role"]) && $_SESSION["role"] == "seller") { ?><a href="seller-dashboard.php">Sell</a><?php } ?>
        <div class="nav-icons">
            <?php $cartCount = 0; if(isset($_SESSION["user_id"])) { $c = $conn->prepare("SELECT COUNT(*) AS total FROM CART_ITEMS ci INNER JOIN CART c ON ci.cart_id = c.cart_id WHERE c.user_id = ?"); $c->bind_param("i", $_SESSION["user_id"]); $c->execute(); $cr = $c->get_result()->fetch_assoc(); $cartCount = $cr["total"]; } ?>
            <a href="cart.php" class="bag-icon"><i class="fa-solid fa-bag-shopping"></i><?php if($cartCount > 0) { ?><span class="cart-count"><?php echo $cartCount; ?></span><?php } ?></a>
            <?php if(isset($_SESSION["user_id"])) { $msgLink = (isset($_SESSION["role"]) && $_SESSION["role"] == "admin") ? "admin-messages.php" : "seller-dashboard.php"; $unreadCount = 0; $u = $conn->prepare("SELECT COUNT(*) AS total FROM MESSAGES WHERE receiver_id = ? AND is_read = 0"); $u->bind_param("i", $_SESSION["user_id"]); $u->execute(); $ur = $u->get_result()->fetch_assoc(); $unreadCount = $ur['total']; ?>
                <a href="<?php echo $msgLink; ?>" class="user-icon" style="position:relative;"><i class="fa-solid fa-bell"></i><?php if($unreadCount > 0) { ?><span style="position:absolute; top:-8px; right:-10px; background:#c65f5f; color:white; font-size:10px; width:18px; height:18px; border-radius:50%; display:flex; justify-content:center; align-items:center; font-weight:bold;"><?php echo $unreadCount; ?></span><?php } ?></a>
                <a href="logout.php" class="user-icon"><i class="fa-solid fa-right-from-bracket"></i></a>
            <?php } else { ?><a href="login.php" class="user-icon"><i class="fa-solid fa-user"></i></a><?php } ?>
        </div>
    </div>
</nav>
<div class="details-container">
    <a href="shop.php" class="back">← Back to Shop</a>
    <div class="product-layout">
        <div><img src="<?php echo htmlspecialchars($product['image']); ?>" class="main-image"><div class="thumbs"><img src="<?php echo htmlspecialchars($product['image']); ?>"></div></div>
        <div class="product-details">
            <p class="brand"><?php echo htmlspecialchars($product['brand']); ?></p>
            <h1><?php echo htmlspecialchars($product['title']); ?></h1>
            <div class="price-line"><span>R<?php echo $product['price']; ?></span><span class="tag">Excellent</span></div>
            <form method="POST"><button type="submit" name="add_to_cart" class="main-btn">🛍 Add to Cart</button></form>
            <?php if($message != ""){ ?><p class="success-message"><?php echo $message; ?></p><?php } ?>
            <div class="page-section"><h3>Description</h3><p><?php echo htmlspecialchars($product['description']); ?></p></div>
            <div class="page-section" style="border-top: 1px solid #ddd; padding-top: 20px; margin-top: 20px;">
                <h3>💬 Message the Seller</h3>
                <form method="POST">
                    <div style="margin-bottom: 10px;"><input type="text" name="subject" placeholder="Subject (e.g. Question about size)" required style="width:100%; padding:10px; border:1px solid #ddd;"></div>
                    <div style="margin-bottom: 10px;"><textarea name="message_text" placeholder="Ask the seller about condition, shipping, etc..." required style="width:100%; padding:10px; border:1px solid #ddd; height:80px; resize:none;"></textarea></div>
                    <button type="submit" name="send_message" style="background: white; border: 1px solid #2f6b57; color: #2f6b57; padding: 10px 20px; cursor: pointer; font-weight: bold;">Send Message</button>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>