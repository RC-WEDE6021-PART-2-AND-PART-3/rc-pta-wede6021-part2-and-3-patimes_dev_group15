
<?php
session_start();
$cartCount = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pastimes</title>
    <link rel="stylesheet" href="css/styling.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
        <a href="cart.php" class="bag-icon">
                <i class="fa-solid fa-bag-shopping"></i>
                <span class="cart-count"><?php echo $cartCount; ?></span>
            </a>
            <a href="login.php" class="user-icon">
                <i class="fa-solid fa-user"></i>
            </a>
        </div>
    </div>
</nav>

<section class="hero">
    <div class="hero-content">
        <h1>Luxury lives <br><span>beyond once</span></h1>

        <p>
            Curated second-hand designer pieces in exceptional condition.
            Each item verified for authenticity and quality.
        </p>

        <div class="hero-buttons">
            <a href="shop.php" class="green-btn">Explore Collection →</a>
            <a href="register.php" class="light-btn">Become a Seller</a>
        </div>
    </div>
</section>

<section class="features">
    <div class="feature">
        <div class="feature-icon">♢</div>
        <h3>Verified Sellers</h3>
        <p>Every seller is carefully vetted and approved by our team to ensure authenticity and quality.</p>
    </div>

    <div class="feature">
        <div class="feature-icon">○</div>
        <h3>Direct Messaging</h3>
        <p>Connect directly with sellers to ask questions and negotiate.</p>
    </div>

    <div class="feature">
        <div class="feature-icon">↻</div>
        <h3>Real-Time Updates</h3>
        <p>Inventory updates in real time, so you never miss out on a piece you love.</p>
    </div>
</section>

<section class="featured">
    <h2>Featured Collection</h2>
    <p class="featured-subtitle">Handpicked pieces from our verified sellers</p>

    <div class="product-grid">
        <a href="product-details.php?id=1" class="product-card">
            <img src="images/scarf.jpg">
            <div class="product-info">
                <p class="brand">Hermès</p>
                <h3>Vintage Hermès Silk Scarf</h3>
                <div class="price-row"><span>R245</span><span class="tag">Excellent</span></div>
            </div>
        </a>

        <a href="product-details.php?id=2" class="product-card">
            <img src="images/trench.jpg">
            <div class="product-info">
                <p class="brand">Burberry</p>
                <h3>Classic Burberry Trench Coat</h3>
                <div class="price-row"><span>R890</span><span class="tag">Like New</span></div>
            </div>
        </a>

        <a href="product-details.php?id=3" class="product-card">
            <img src="images/shoes.jpg">
            <div class="product-info">
                <p class="brand">Gucci</p>
                <h3>Gucci Leather Loafers</h3>
                <div class="price-row"><span>R425</span><span class="tag">Excellent</span></div>
            </div>
        </a>

        <a href="product-details.php?id=4" class="product-card">
            <img src="images/bag.jpg">
            <div class="product-info">
                <p class="brand">Chanel</p>
                <h3>Chanel Classic Handbag</h3>
                <div class="price-row"><span>R3200</span><span class="tag">Very Good</span></div>
            </div>
        </a>
    </div>

    <a href="shop.php" class="view-btn">View All Items →</a>
</section>

<section class="sell-section">
    <h2>Have luxury pieces to sell?</h2>
    <p>Join our community of verified sellers and reach buyers who appreciate quality.</p>
    <a href="register.php">Apply to Sell</a>
</section>

<footer>
    <div class="footer-grid">
        <div>
            <h4>Pastimes</h4>
            <p>Curated secondhand luxury clothing in exceptional condition.</p>
        </div>

        <div>
            <h4>Shop</h4>
            <a href="shop.php">All Items</a>
            <a href="#">Women</a>
            <a href="#">Men</a>
            <a href="#">Accessories</a>
        </div>

        <div>
            <h4>Support</h4>
            <a href="#">FAQ</a>
            <a href="#">Shipping</a>
            <a href="#">Returns</a>
            <a href="contact.php">Contact</a>
        </div>

        <div>
            <h4>Sell With Us</h4>
            <p>Interested in selling your luxury items?</p>
            <a href="register.php" class="footer-btn">Get Started</a>
        </div>
    </div>

    <p class="copyright">© 2026 Pastimes. All rights reserved.</p>
</footer>

</body>
</html>