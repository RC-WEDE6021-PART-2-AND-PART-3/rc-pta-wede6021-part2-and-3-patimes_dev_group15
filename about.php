


<?php
session_start();
$cartCount = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>About | Pastimes</title>
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
    <h1>Our Story</h1>

    <p>
        Pastimes was founded with a single mission: to redefine luxury secondhand shopping.
        We believe that exceptional design and craftsmanship should be celebrated and passed on.
    </p>

    <p>
        Every piece in our collection is handpicked and verified by our team of experts.
        We focus on high-quality items from the world's most prestigious fashion houses.
    </p>

    <div class="about-columns">
        <div>
            <h3>Curated Quality</h3>
            <p>We curate timeless pieces that reflect excellence and sustainability in fashion.</p>
        </div>

        <div>
            <h3>Verified Authenticity</h3>
            <p>Every seller is vetted and every item is inspected before it reaches you.</p>
        </div>
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