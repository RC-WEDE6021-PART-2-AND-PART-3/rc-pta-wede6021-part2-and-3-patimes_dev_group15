<?php session_start(); include "DBConn.php"; if (!isset($_SESSION["user_id"])) { header("Location: login.php"); exit(); } $user_id = $_SESSION["user_id"]; if (isset($_GET["remove"])) { $cart_item_id = $_GET["remove"]; $stmt = $conn->prepare("DELETE ci FROM CART_ITEMS ci INNER JOIN CART c ON ci.cart_id = c.cart_id WHERE ci.cart_item_id = ? AND c.user_id = ?"); $stmt->bind_param("ii", $cart_item_id, $user_id); $stmt->execute(); header("Location: cart.php"); exit(); } $stmt = $conn->prepare("SELECT ci.cart_item_id, ci.quantity, i.item_id, i.title, i.price, i.image FROM CART_ITEMS ci INNER JOIN CART c ON ci.cart_id = c.cart_id INNER JOIN ITEMS i ON ci.item_id = i.item_id WHERE c.user_id = ?"); $stmt->bind_param("i", $user_id); $stmt->execute(); $result = $stmt->get_result(); $total = 0; $shipping = 15; ?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Shopping Cart | Pastimes</title><link rel="stylesheet" href="css/styling.css"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"></head>
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
<div class="cart-container">
    <h1>Shopping Cart</h1>
    <?php if($result->num_rows == 0) { ?>
        <div class="empty-cart"><div class="empty-icon"><i class="fa-solid fa-bag-shopping"></i></div><h2>Your cart is empty</h2><p>Start adding items to your cart</p><a href="shop.php" class="green-btn">Continue Shopping</a></div>
    <?php } else { ?>
        <div class="cart-layout">
            <div>
                <?php while($row = $result->fetch_assoc()) { $subtotal = $row["price"] * $row["quantity"]; $total += $subtotal; ?>
                    <div class="cart-item">
                        <img src="<?php echo htmlspecialchars($row["image"]); ?>">
                        <div>
                            <p class="brand">Pastimes</p><h3><?php echo htmlspecialchars($row["title"]); ?></h3><p class="seller">by Verified Seller</p><p class="price">R<?php echo $row["price"]; ?></p>
                            <div class="quantity-box"><a href="update-cart.php?id=<?php echo $row['cart_item_id']; ?>&action=decrease" style="text-decoration:none; color:#2f6b57; font-size:20px; padding:8px 12px;">−</a><span style="font-weight:bold;"><?php echo $row["quantity"]; ?></span><a href="update-cart.php?id=<?php echo $row['cart_item_id']; ?>&action=increase" style="text-decoration:none; color:#2f6b57; font-size:20px; padding:8px 12px;">+</a></div>
                        </div>
                        <a href="cart.php?remove=<?php echo $row['cart_item_id']; ?>" class="remove">Remove</a>
                    </div>
                <?php } ?>
            </div>
            <div class="summary">
                <h3>Order Summary</h3>
                <div class="summary-row"><span>Subtotal</span><span>R<?php echo $total; ?></span></div>
                <div class="summary-row"><span>Shipping</span><span>R<?php echo $shipping; ?></span></div>
                <div class="summary-row summary-total"><span>Total</span><span>R<?php echo $total + $shipping; ?></span></div>
                <a href="checkout.php" class="checkout-btn">Proceed to Checkout →</a><a href="shop.php" class="continue-btn">Continue Shopping</a>
            </div>
        </div>
    <?php } ?>
</div>
</body>
</html>