<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Order Placed | Pastimes</title><link rel="stylesheet" href="css/styling.css"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"></head>
<body>
<nav class="navbar">
    <div class="logo">Pastimes</div>
    <div class="nav-links"><a href="index.php">Home</a><a href="shop.php">Shop</a><a href="about.php">About</a><a href="contact.php">Contact</a></div>
</nav>
<div class="success-wrapper" style="min-height:80vh; display:flex; flex-direction:column; align-items:center; justify-content:center; text-align:center; background:#f8f6f2; padding:40px 20px;">
    <div class="success-icon" style="font-size:60px; color:#2f6b57; margin-bottom:20px;"><i class="fa-regular fa-circle-check"></i></div>
    <h1 style="font-family:Georgia, serif; font-size:42px; color:#2d241f; margin-bottom:10px;">Order Placed Successfully!</h1>
    <p style="font-size:18px; color:#555; margin-bottom:30px;">Your items will be shipped soon. You will receive a tracking number via email.</p>
    <a href="shop.php" class="green-btn" style="padding:14px 40px; text-decoration:none; display:inline-block;">Continue Shopping →</a>
</div>
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