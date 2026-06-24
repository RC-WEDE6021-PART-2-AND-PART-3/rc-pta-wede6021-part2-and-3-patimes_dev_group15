<?php session_start(); include "DBConn.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Contact | Pastimes</title><link rel="stylesheet" href="css/styling.css"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"></head>
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
<section class="content-section">
    <h1>Contact Us</h1>
    <p>We're here to help with any questions you might have about our collection, your order, or how to become a verified seller.</p>
    <div class="contact-grid">
        <div>
            <h3>SUPPORT</h3><p>support@pastimes.com<br>+27 (0) 21 555 0123</p>
            <h3>PRESS & PARTNERSHIPS</h3><p>press@pastimes.com</p>
            <h3>HEADQUARTERS</h3><p>V&A Waterfront<br>Cape Town, 8001<br>South Africa</p>
        </div>
        <form class="contact-form"><label>Name</label><input type="text"><label>Email</label><input type="email"><label>Message</label><textarea></textarea><button class="main-btn">Send Message</button></form>
    </div>
</section>
<footer>
    <div class="footer-grid">
        <div><h4>Pastimes</h4><p>Curated secondhand luxury clothing in exceptional condition.</p></div>
        <div><h4>Shop</h4><a href="shop.php">All Items</a><a href="#">Women</a><a href="#">Men</a><a href="#">Accessories</a></div>
        <div><h4>Support</h4><a href="#">FAQ</a><a href="#">Shipping</a><a href="#">Returns</a><a href="contact.php">Contact</a></div>
        <div><h4>Sell With Us</h4><p>Interested in selling your luxury items?</p><a href="register.php" class="footer-btn">Get Started</a></div>
    </div>
    <p class="copyright">© 2026 Pastimes. All rights reserved.</p>
</footer>
</body>
</html>