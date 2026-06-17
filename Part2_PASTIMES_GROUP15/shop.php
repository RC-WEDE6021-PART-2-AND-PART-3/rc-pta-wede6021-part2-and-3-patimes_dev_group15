<?php
session_start();
include "DBConn.php";

$cartCount = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;

$items = $conn->query("SELECT * FROM ITEMS");
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

<section class="shop-heading">
    <h1>Shop Collection</h1>
    <p>Curated pieces from verified sellers</p>
</section>

<section class="shop-area">

    <aside class="filters">
        <div class="filter-top">
            <h3>Filters</h3>
            <a href="shop.php">Reset</a>
        </div>

        <h4>Search</h4>
        <input class="search" type="text" placeholder="Search items...">

        <h4>Category</h4>
        <label><input type="radio" checked> All Items</label>
        <label><input type="radio"> Women</label>
        <label><input type="radio"> Men</label>
        <label><input type="radio"> Accessories</label>

        <h4>Condition</h4>
        <label><input type="radio" checked> All Conditions</label>
        <label><input type="radio"> Like New</label>
        <label><input type="radio"> Excellent</label>
        <label><input type="radio"> Very Good</label>

        <h4>Price Range: R0 - R5000</h4>
        <input type="range">
    </aside>

    <div class="products">

        <?php while($item = $items->fetch_assoc()){ ?>

            <a class="product-card" href="product-details.php?id=<?php echo $item['item_id']; ?>">
                <img src="<?php echo $item['image']; ?>">

                <div class="product-info">
                    <p class="brand">Pastimes</p>
                    <h3><?php echo htmlspecialchars($item['title']); ?></h3>
                    <p class="seller">by Verified Seller</p>

                    <div class="price-row">
                        <span>R<?php echo $item['price']; ?></span>
                        <span class="tag">Excellent</span>
                    </div>
                </div>
            </a>

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

    <p class="copyright">©️ 2026 Pastimes. All rights reserved.</p>
</footer>

</body>
</html>