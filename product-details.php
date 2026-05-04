<?php
session_start();
include "DBConn.php";

$message = "";

$id = $_GET['id'] ?? 1;

$stmt = $conn->prepare("SELECT * FROM ITEMS WHERE item_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Product not found.");
}

$product = $result->fetch_assoc();

if (isset($_POST['add_to_cart'])) {

    if (!isset($_SESSION["user_id"])) {
        $message = "Please login before adding items to cart.";
    } else {
        $user_id = $_SESSION["user_id"];

        $cartCheck = $conn->prepare("SELECT cart_id FROM CART WHERE user_id = ?");
        $cartCheck->bind_param("i", $user_id);
        $cartCheck->execute();
        $cartResult = $cartCheck->get_result();

        if ($cartResult->num_rows == 0) {
            $createCart = $conn->prepare("INSERT INTO CART (user_id) VALUES (?)");
            $createCart->bind_param("i", $user_id);
            $createCart->execute();
            $cart_id = $createCart->insert_id;
        } else {
            $cartRow = $cartResult->fetch_assoc();
            $cart_id = $cartRow["cart_id"];
        }

        $itemCheck = $conn->prepare("SELECT * FROM CART_ITEMS WHERE cart_id = ? AND item_id = ?");
        $itemCheck->bind_param("ii", $cart_id, $id);
        $itemCheck->execute();
        $itemResult = $itemCheck->get_result();

        if ($itemResult->num_rows > 0) {
            $update = $conn->prepare("UPDATE CART_ITEMS SET quantity = quantity + 1 WHERE cart_id = ? AND item_id = ?");
            $update->bind_param("ii", $cart_id, $id);
            $update->execute();
        } else {
            $insert = $conn->prepare("INSERT INTO CART_ITEMS (cart_id, item_id, quantity) VALUES (?, ?, 1)");
            $insert->bind_param("ii", $cart_id, $id);
            $insert->execute();
        }

        $message = "Added to cart";
    }
}

$cartCount = 0;

if (isset($_SESSION["user_id"])) {
    $countStmt = $conn->prepare("
        SELECT COUNT(*) AS total 
        FROM CART_ITEMS ci
        INNER JOIN CART c ON ci.cart_id = c.cart_id
        WHERE c.user_id = ?
    ");
    $countStmt->bind_param("i", $_SESSION["user_id"]);
    $countStmt->execute();
    $countResult = $countStmt->get_result()->fetch_assoc();
    $cartCount = $countResult["total"];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($product['title']); ?> | Pastimes</title>
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

<div class="details-container">

    <a href="shop.php" class="back">← Back to Shop</a>

    <div class="product-layout">

        <div>
            <img src="<?php echo htmlspecialchars($product['image']); ?>" class="main-image">

            <div class="thumbs">
                <img src="<?php echo htmlspecialchars($product['image']); ?>">
                <img src="images/bag.jpg">
                <img src="images/glasses.jpg">
            </div>
        </div>

        <div class="product-details">

            <p class="brand">Pastimes</p>

            <h1><?php echo htmlspecialchars($product['title']); ?></h1>

            <div class="price-line">
                <span>R<?php echo $product['price']; ?></span>
                <span class="tag">Excellent</span>
            </div>

            <form method="POST">
                <button type="submit" name="add_to_cart" class="main-btn">
                    🛍 Add to Cart
                </button>
            </form>

            <?php if($message != ""){ ?>
                <p class="success-message"><?php echo $message; ?></p>
            <?php } ?>

            <div class="small-buttons">
                <button>♡ Message</button>
                <button>♡ Save</button>
            </div>

            <div class="seller-box">
                <div>
                    <strong>Verified Seller</strong>
                    <p>127 sales · 4.9 rating · Joined 2024</p>
                </div>
                <a href="#">View Profile</a>
            </div>

            <div class="page-section">
                <h3>Description</h3>
                <p><?php echo htmlspecialchars($product['description']); ?></p>
            </div>

            <div class="page-section">
                <h3>Details</h3>

                <div class="details-table">
                    <div class="detail-row">
                        <span>Brand</span>
                        <span>Pastimes</span>
                    </div>

                    <div class="detail-row">
                        <span>Condition</span>
                        <span>Excellent</span>
                    </div>

                    <div class="detail-row">
                        <span>Price</span>
                        <span>R<?php echo $product['price']; ?></span>
                    </div>
                </div>
            </div>

            <div class="shipping-box">
                <strong>Shipping Information</strong><br>
                Carefully packaged and shipped within 2–3 business days.
                Tracking information will be provided.
            </div>

        </div>

    </div>

</div>

</body>
</html>