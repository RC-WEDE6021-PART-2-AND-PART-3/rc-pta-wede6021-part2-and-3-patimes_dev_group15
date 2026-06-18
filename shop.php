<?php 
session_start(); 
include "DBConn.php"; 
$items = $conn->query("SELECT * FROM ITEMS WHERE approved = 1");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Shop Collection | Pastimes</title>
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

        <?php if(isset($_SESSION["role"]) && $_SESSION["role"] == "seller") { ?>
            <a href="seller-dashboard.php">Sell</a>
        <?php } ?>

        <div class="nav-icons">
            <!-- CART ICON -->
            <a href="cart.php" class="bag-icon">
                <i class="fa-solid fa-bag-shopping"></i>
                <span class="cart-count">0</span>
            </a>

            <!-- BELL ICON (Only shows for Customers & Sellers, NOT Admin) -->
            <?php 
            if(isset($_SESSION["user_id"]) && isset($_SESSION["role"]) && $_SESSION["role"] != "admin") { 
                $msgLink = ($_SESSION["role"] == "seller") ? "seller-dashboard.php" : "my-messages.php";
            ?>
                <a href="<?php echo $msgLink; ?>" class="user-icon" style="position:relative; text-decoration:none; color:#2f6b57; font-size:20px;">
                    <i class="fa-solid fa-bell"></i>
                </a>
            <?php } ?>

            <!-- LOGIN / LOGOUT ICON -->
            <?php if(isset($_SESSION["user_id"])) { ?>
                <a href="logout.php" class="user-icon">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </a>
            <?php } else { ?>
                <a href="login.php" class="user-icon">
                    <i class="fa-solid fa-user"></i>
                </a>
            <?php } ?>
        </div>
    </div>
</nav>

<section class="shop-heading">
    <h1>Shop Collection</h1>
    <p>Curated pieces from verified sellers</p>
</section>

<section class="shop-area" style="display: block; padding: 35px 24% 70px;">
    <div class="products" style="grid-template-columns: repeat(3, 1fr); gap: 25px;">
        <?php if($items->num_rows > 0) { while($item = $items->fetch_assoc()){ ?>
            <a class="product-card" href="product-details.php?id=<?php echo $item['item_id']; ?>">
                <img src="<?php echo htmlspecialchars($item['image']); ?>">
                <div class="product-info">
                    <p class="brand"><?php echo htmlspecialchars($item['brand']); ?></p>
                    <h3><?php echo htmlspecialchars($item['title']); ?></h3>
                    <p class="seller">by Verified Seller</p>
                    <div class="price-row">
                        <span>R<?php echo number_format($item['price'], 2); ?></span>
                        <span class="tag">Excellent</span>
                    </div>
                </div>
            </a>
        <?php } } else { ?>
            <p style="grid-column: 1/-1; text-align: center; padding: 40px; color: #777;">No products found.</p>
        <?php } ?>
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