<?php session_start(); include "DBConn.php"; if (!isset($_SESSION["user_id"])) { header("Location: login.php"); exit(); } $user_id = $_SESSION["user_id"]; $stmt = $conn->prepare("SELECT ci.quantity, i.title, i.price FROM CART_ITEMS ci INNER JOIN CART c ON ci.cart_id = c.cart_id INNER JOIN ITEMS i ON ci.item_id = i.item_id WHERE c.user_id = ?"); $stmt->bind_param("i", $user_id); $stmt->execute(); $result = $stmt->get_result(); $total = 0; $shipping = 15; ?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Checkout | Pastimes</title><link rel="stylesheet" href="css/styling.css"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"></head>
<body>
<nav class="navbar">
    <div class="logo">Pastimes</div>
    <div class="nav-links"><a href="index.php">Home</a><a href="shop.php">Shop</a><a href="about.php">About</a><a href="contact.php">Contact</a>
    <?php if(isset($_SESSION["role"]) && $_SESSION["role"] == "seller") { ?><a href="seller-dashboard.php">Sell</a><?php } ?></div>
</nav>
<div class="checkout-container">
    <h1>Checkout</h1>
    <div class="checkout-layout">
        <form method="POST" action="process-order.php">
            <div>
                <div class="checkout-section">
                    <div class="section-title">📍 Shipping Information</div>
                    <div class="form-group"><label>Full Name</label><input type="text" name="fullname" required></div>
                    <div class="form-row"><div><label>Email</label><input type="email" name="email" required></div><div><label>Phone</label><input type="text" name="phone" required></div></div>
                    <div class="form-group"><label>Address</label><input type="text" name="address" required></div>
                    <div class="form-row"><div><label>City</label><input type="text" name="city" required></div><div><label>State/Province</label><input type="text" name="state" required></div></div>
                    <div class="form-row"><div><label>ZIP/Postal Code</label><input type="text" name="zip" required></div><div><label>Country</label><input type="text" name="country" required></div></div>
                </div>
                <div class="checkout-section">
                    <div class="section-title">💳 Payment Information</div>
                    <div class="payment-note">Your payment information is secure and encrypted.<br>This is a demo checkout.</div>
                    <div class="form-group"><label>Card Number</label><input type="text" name="card_number" placeholder="1234 5678 9012 3456" required></div>
                    <div class="form-group"><label>Cardholder Name</label><input type="text" name="card_name" required></div>
                    <div class="form-row"><div><label>Expiry Date</label><input type="text" name="expiry" placeholder="MM/YY" required></div><div><label>CVV</label><input type="text" name="cvv" placeholder="123" required></div></div>
                </div>
            </div>
            <div class="summary">
                <h3>Order Summary</h3>
                <?php while($row = $result->fetch_assoc()) { $subtotal = $row["price"] * $row["quantity"]; $total += $subtotal; ?>
                    <div class="summary-item"><span><?php echo $row["title"]; ?></span><span>R<?php echo $row["price"]; ?></span></div>
                <?php } ?>
                <div class="summary-row"><span>Subtotal</span><span>R<?php echo $total; ?></span></div>
                <div class="summary-row"><span>Shipping</span><span>R<?php echo $shipping; ?></span></div>
                <div class="summary-row summary-total"><span>Total</span><span>R<?php echo $total + $shipping; ?></span></div>
                <button type="submit" class="place-order">Place Order</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>