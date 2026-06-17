<?php
session_start();
include "DBConn.php";

// Mark all unread messages as read because the seller is viewing them
if(isset($_SESSION["user_id"])) {
    $conn->query("UPDATE MESSAGES SET is_read = 1 WHERE receiver_id = " . $_SESSION["user_id"]);
}

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "seller") {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$message = "";

// HANDLE SUBMITTING ITEMS
if (isset($_POST["submitItem"])) {
    $title = trim($_POST["title"]);
    $brand = trim($_POST["brand"]);
    $description = trim($_POST["description"]);
    $price = trim($_POST["price"]);
    
    // Handle Image Upload
    $targetDir = __DIR__ . "/images/";
    if(isset($_FILES["itemImage"]) && $_FILES["itemImage"]["error"] == 0) {
        $fileName = basename($_FILES["itemImage"]["name"]);
        $targetFilePath = $targetDir . $fileName;
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
        $allowTypes = array('jpg','png','jpeg','gif');
        
        if(in_array($fileType, $allowTypes)) {
            $uniqueName = time() . "_" . $fileName;
            $targetFilePath = $targetDir . $uniqueName;
            if(move_uploaded_file($_FILES["itemImage"]["tmp_name"], $targetFilePath)) {
                $imagePath = "images/" . $uniqueName;
                $stmt = $conn->prepare("INSERT INTO ITEMS (user_id, title, brand, description, price, image, approved) VALUES (?, ?, ?, ?, ?, ?, 0)");
                $stmt->bind_param("isssds", $user_id, $title, $brand, $description, $price, $imagePath);
                if ($stmt->execute()) {
                    $message = "✅ Your item has been uploaded and submitted for review!";
                } else { $message = "❌ Database error."; }
            } else { $message = "❌ Failed to move uploaded file."; }
        } else { $message = "❌ Invalid file type."; }
    } else { $message = "❌ Please select an image."; }
}

// HANDLE REPLYING TO A MESSAGE
if (isset($_POST['reply_message'])) {
    $receiver_id = $_POST['buyer_id']; 
    $msg_subject = "RE: " . trim($_POST['subject']);
    $msg_text = trim($_POST['reply_text']);
    $item_id = $_POST['item_id'];

    $stmt = $conn->prepare("INSERT INTO MESSAGES (sender_id, receiver_id, item_id, subject, message) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iiiss", $user_id, $receiver_id, $item_id, $msg_subject, $msg_text);
    $stmt->execute();
    $message = "✅ Reply sent to buyer!";
}

// Fetch seller's items
$myItems = $conn->query("SELECT * FROM ITEMS WHERE user_id = $user_id ORDER BY item_id DESC");
// Fetch incoming messages
$inbox = $conn->query("SELECT m.*, u.name as sender_name, i.title as item_title FROM MESSAGES m JOIN USERS u ON m.sender_id = u.user_id LEFT JOIN ITEMS i ON m.item_id = i.item_id WHERE m.receiver_id = $user_id ORDER BY m.date_sent DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Seller Dashboard | Pastimes</title>
    <link rel="stylesheet" href="css/styling.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Custom CSS to make the right side form BIGGER and nicer */
        .big-form-container {
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }
        .big-form-container h3 {
            font-size: 20px;
            margin-bottom: 25px;
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
        }
        .big-form-container label {
            display: block;
            font-weight: bold;
            margin-bottom: 6px;
            font-size: 14px;
            color: #333;
        }
        .big-form-container input, 
        .big-form-container textarea {
            width: 100%;
            padding: 14px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 20px;
            font-size: 15px;
            background: #fcfcfc;
        }
        .big-form-container textarea {
            height: 120px;
            resize: none;
        }
        .big-form-container input[type="file"] {
            padding: 10px;
            background: #f8f6f2;
        }
        .big-submit-btn {
            width: 100%;
            padding: 16px;
            background: #2f6b57;
            color: white;
            border: none;
            border-radius: 4px;
            font-weight: bold;
            font-size: 16px;
            cursor: pointer;
            transition: 0.2s;
        }
        .big-submit-btn:hover {
            background: #1d4d3e;
        }
        
        /* Message container styling */
        .msg-inbox-container {
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 25px;
            min-height: 300px;
        }
        .single-message {
            border-bottom: 1px solid #eee;
            padding: 15px 0;
        }
        .single-message:last-child {
            border-bottom: none;
        }
    </style>
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
                <span class="cart-count"><?php echo $cartCount; ?></span>
            </a>

            <!-- NOTIFICATION BELL ICON (Only shows if logged in) -->
            <?php if(isset($_SESSION["user_id"])) { 
                // Determine where the bell should link based on role
                $msgLink = (isset($_SESSION["role"]) && $_SESSION["role"] == "admin") ? "admin-messages.php" : "seller-dashboard.php";
                
                // Safely fetch the unread count directly in PHP
                $checkStmt = $conn->prepare("SELECT COUNT(*) AS total FROM MESSAGES WHERE receiver_id = ? AND is_read = 0");
                $checkStmt->bind_param("i", $_SESSION["user_id"]);
                $checkStmt->execute();
                $checkResult = $checkStmt->get_result();
                $checkRow = $checkResult->fetch_assoc();
                $unreadCount = $checkRow['total'];
            ?>
                <a href="<?php echo $msgLink; ?>" class="user-icon" style="position:relative; text-decoration:none; color:#2f6b57; font-size:20px;">
                    <i class="fa-solid fa-bell"></i>
                    
                    <!-- Unread Count Badge -->
                    <?php if($unreadCount > 0) { ?>
                        <span style="position:absolute; top:-8px; right:-10px; background:#c65f5f; color:white; font-size:10px; width:18px; height:18px; border-radius:50%; display:flex; justify-content:center; align-items:center; font-weight:bold;">
                            <?php echo $unreadCount; ?>
                        </span>
                    <?php } ?>
                </a>
            <?php } ?>

            <!-- LOGIN / LOGOUT ICON -->
            <?php if(isset($_SESSION["user_id"])) { ?>
                <a href="logout.php" class="user-icon" title="Logout">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </a>
            <?php } else { ?>
                <a href="login.php" class="user-icon" title="Login">
                    <i class="fa-solid fa-user"></i>
                </a>
            <?php } ?>

        </div>
    </div>
</nav>

<div style="padding: 60px 24%; background: #f8f6f2; min-height: 80vh;">
    
    <h1 style="font-family: Georgia, serif; font-weight: 400; font-size: 38px; margin-bottom: 10px;">Seller Dashboard</h1>
    <p style="color: #777; margin-bottom: 40px;">Manage your items and talk to buyers.</p>

    <?php if($message != "") { ?>
        <div style="background: white; border: 1px solid #ddd; padding: 20px; margin-bottom: 30px; color: #2f6b57; font-weight: bold; border-radius: 4px;">
            <?php echo $message; ?>
        </div>
    <?php } ?>

    <!-- NEW LAYOUT: 2 Columns, Left is Messages, Right is BIG Form -->
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 40px; align-items: start;">
        
        <!-- LEFT COLUMN: BUYER MESSAGES (Larger area) -->
        <div class="msg-inbox-container">
            <h3 style="margin-bottom: 20px; font-size: 20px; border-bottom: 1px solid #eee; padding-bottom: 15px;">
                📨 Buyer Messages
            </h3>
            
            <?php if($inbox->num_rows == 0) { ?>
                <div style="text-align: center; padding: 40px 0; color: #888;">
                    <i class="fa-regular fa-envelope" style="font-size: 30px; margin-bottom: 10px; display: block;"></i>
                    No messages from buyers yet.
                </div>
            <?php } else { while($msg = $inbox->fetch_assoc()) { ?>
                <div class="single-message">
                    <p style="font-weight: bold; color: #2f6b57; margin-bottom: 4px;">
                        <i class="fa-solid fa-user"></i> <?php echo htmlspecialchars($msg['sender_name']); ?> 
                        <span style="font-weight: normal; color:#888; font-size: 13px; margin-left:10px;">
                            (Item: <?php echo htmlspecialchars($msg['item_title']); ?>)
                        </span>
                    </p>
                    <p style="font-size: 13px; color:#666; margin-bottom: 8px;"><strong>Subject:</strong> <?php echo htmlspecialchars($msg['subject']); ?></p>
                    <p style="color:#333; background: #f9f9f9; padding: 10px; border-radius: 4px; margin-bottom: 10px;">
                        <?php echo htmlspecialchars($msg['message']); ?>
                    </p>
                    
                    <!-- Reply Form -->
                    <form method="POST" style="margin-top: 10px; display: flex; gap: 10px; flex-wrap: wrap;">
                        <input type="hidden" name="buyer_id" value="<?php echo $msg['sender_id']; ?>">
                        <input type="hidden" name="item_id" value="<?php echo $msg['item_id']; ?>">
                        <input type="hidden" name="subject" value="<?php echo htmlspecialchars($msg['subject']); ?>">
                        
                        <textarea name="reply_text" placeholder="Type your reply..." required style="flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 4px; min-width: 200px; resize: none; height: 45px;"></textarea>
                        <button type="submit" name="reply_message" style="background:#2f6b57; color:white; border:none; padding: 0 20px; border-radius: 4px; cursor:pointer; font-weight:bold;">Reply</button>
                    </form>
                </div>
            <?php } } ?>
        </div>

        <!-- RIGHT COLUMN: ADD NEW ITEM (Big, beautiful form) -->
        <div class="big-form-container">
            <h3>📦 Add New Item</h3>
            
            <form method="POST" enctype="multipart/form-data">
                <label>Item Name</label>
                <input type="text" name="title" placeholder="e.g. Vintage Leather Jacket" required>

                <label>Brand</label>
                <input type="text" name="brand" placeholder="e.g. Saint Laurent" required>

                <label>Description</label>
                <textarea name="description" placeholder="Describe the item condition, size, and unique features..." required></textarea>

                <label>Price (R)</label>
                <input type="number" step="0.01" name="price" placeholder="e.g. 1450.00" required>

                <label>Upload Image</label>
                <input type="file" name="itemImage" accept="image/*" required>

                <button type="submit" name="submitItem" class="big-submit-btn">Submit for Approval</button>
            </form>
        </div>

    </div>
</div>

</body>
</html>