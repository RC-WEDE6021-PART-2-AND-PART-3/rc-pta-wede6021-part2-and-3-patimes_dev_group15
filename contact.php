

<?php
session_start();
$cartCount = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact | Pastimes</title>
    <link rel="stylesheet" href="css/styling.css">
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
            <a href="cart.php" class="bag-icon">🛍
                <span class="cart-count"><?php echo $cartCount; ?></span>
            </a>
            <a href="login.php" class="user-icon">♡</a>
        </div>
    </div>
</nav>

<section class="content-section">
    <h1>Contact Us</h1>

    <p>
        We're here to help with any questions you might have about our collection,
        your order, or how to become a verified seller.
    </p>

    <div class="contact-grid">

        <div>
            <h3>SUPPORT</h3>
            <p>support@pastimes.com<br>+27 (0) 21 555 0123</p>

            <h3>PRESS & PARTNERSHIPS</h3>
            <p>press@pastimes.com</p>

            <h3>HEADQUARTERS</h3>
            <p>V&A Waterfront<br>Cape Town, 8001<br>South Africa</p>
        </div>

        <form class="contact-form">
            <label>Name</label>
            <input type="text">

            <label>Email</label>
            <input type="email">

            <label>Message</label>
            <textarea></textarea>

            <button class="main-btn">Send Message</button>
        </form>

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