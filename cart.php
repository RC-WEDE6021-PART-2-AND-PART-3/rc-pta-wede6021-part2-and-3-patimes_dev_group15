<?php
// Starts the session so the system can identify the logged-in user.
session_start();

// Includes the database connection file.
include "DBConn.php";

// Checks if the user is logged in.
// 
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Stores the logged-in user's ID.
$user_id = $_SESSION["user_id"];

//REMOVE ITEM FROM CART
  
// 
if (isset($_GET["remove"])) {
    $cart_item_id = $_GET["remove"];

    // Deletes the selected cart item only if it belongs to the logged-in user.
    $stmt = $conn->prepare("
        DELETE ci FROM CART_ITEMS ci
        INNER JOIN CART c ON ci.cart_id = c.cart_id
        WHERE ci.cart_item_id = ? AND c.user_id = ?
    ");

    $stmt->bind_param("ii", $cart_item_id, $user_id);
    $stmt->execute();

    // Redirects back to the cart page after removing the item.
    header("Location: cart.php");
    exit();
}


 //GET CART ITEMS
  
// 
$stmt = $conn->prepare("
    SELECT ci.cart_item_id, ci.quantity, i.item_id, i.title, i.price, i.image
    FROM CART_ITEMS ci
    INNER JOIN CART c ON ci.cart_id = c.cart_id
    INNER JOIN ITEMS i ON ci.item_id = i.item_id
    WHERE c.user_id = ?
");

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Counts how many different items are in the cart.
$cartCount = $result->num_rows;

// Used to calculate the order subtotal.
$total = 0;

// Fixed shipping amount.
$shipping = 15;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Shopping Cart | Pastimes</title>

    <!-- Links this page to the main stylesheet. -->
    <link rel="stylesheet" href="css/styling.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<!-- Navigation bar -->
<nav class="navbar">
    <div class="logo">Pastimes</div>

    <div class="nav-links">
        <a href="index.php">Home</a>
        <a href="shop.php">Shop</a>
        <a href="about.php">About</a>
        <a href="contact.php">Contact</a>

        <!-- Cart and login icons -->
        <div class="nav-icons">
        <a href="cart.php" class="bag-icon"> <?php if ($cartCount > 0) echo 'active'; ?>
                <i class="fa-solid fa-bag-shopping"></i>
                <span class="cart-count"><?php echo $cartCount; ?></span>
            </a>
            <a href="login.php" class="user-icon">
            <i class="fa-solid fa-user"></i>
        </a> s
        </div>
    </div>
</nav>

<!-- Main cart page container -->
<div class="cart-container">

    <h1>Shopping Cart</h1>

    <!-- Empty cart state -->
    <!-- -->
    <?php if ($cartCount == 0) { ?>

        <div class="empty-cart">
            <div class="empty-icon">
                <i class="fa-solid fa-bag-shopping"></i>
            </div>
            <h2>Your cart is empty</h2>
            <p>Start adding items to your cart</p>
            <a href="shop.php" class="green-btn">Continue Shopping</a>
        </div>

    <?php } else { ?>

        <!-- Cart with items layout -->
        <!--  -->
        <div class="cart-layout">

            <div>
                <?php 
                // Loops through each cart item and displays it.
                while($row = $result->fetch_assoc()) { 

                    // Calculates subtotal for each item.
                    $subtotal = $row["price"] * $row["quantity"];

                    // Adds item subtotal to total.
                    $total += $subtotal;
                ?>

                    <div class="cart-item">

                        <!-- Product image -->
                        <img src="<?php echo htmlspecialchars($row["image"]); ?>">

                        <div>
                            <p class="brand">Pastimes</p>

                            <!-- Product title -->
                            <h3><?php echo htmlspecialchars($row["title"]); ?></h3>

                            <p class="seller">by Verified Seller</p>

                            <!-- Product price -->
                            <p class="price">R<?php echo $row["price"]; ?></p>

                            <!-- Quantity display -->
                            <div class="quantity-box">
                                <span>-</span>
                                <span><?php echo $row["quantity"]; ?></span>
                                <span>+</span>
                            </div>
                        </div>

                        <!-- Remove link -->
                       
                        <a href="cart.php?remove=<?php echo $row['cart_item_id']; ?>" class="remove">Remove</a>
                    </div>

                <?php } ?>
            </div>

            <!-- Order summary section -->
            <!--  -->
            <div class="summary">
                <h3>Order Summary</h3>

                <div class="summary-row">
                    <span>Subtotal</span>
                    <span>R<?php echo $total; ?></span>
                </div>

                <div class="summary-row">
                    <span>Shipping</span>
                    <span>R<?php echo $shipping; ?></span>
                </div>

                <div class="summary-row summary-total">
                    <span>Total</span>
                    <span>R<?php echo $total + $shipping; ?></span>
                </div>

                <!-- Goes to checkout page -->
                <a href="checkout.php" class="checkout-btn">Proceed to Checkout →</a>

                <!-- Returns user to shop page -->
                <a href="shop.php" class="continue-btn">Continue Shopping</a>
            </div>

        </div>

    <?php } ?>

</div>

</body>
</html>