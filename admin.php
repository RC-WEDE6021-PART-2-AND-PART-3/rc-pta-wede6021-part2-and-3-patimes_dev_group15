<?php
// FORCE PHP TO SHOW ERRORS
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Starts session to track logged-in user.
session_start();

// Connects to the database.
include "DBConn.php";

// CHECK IF THE CONNECTION IS WORKING
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Checks if user is logged in AND is an admin.
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "admin") {
    header("Location: login.php");
    exit();
}

// ADD USER FUNCTION
if (isset($_POST["addUser"])) {

    $name = $_POST["name"];
    $email = $_POST["email"];
    $username = $_POST["username"];
    $password = md5($_POST["password"]);
    $role = $_POST["role"];
    $status = $_POST["status"];

    $stmt = $conn->prepare("INSERT INTO USERS (name, email, username, password, role, status) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $name, $email, $username, $password, $role, $status);
    $stmt->execute();

    header("Location: admin.php");
    exit();
}

// UPDATE USER FUNCTION
if (isset($_POST["updateUser"])) {

    $id = $_POST["id"];
    $name = $_POST["name"];
    $email = $_POST["email"];
    $username = $_POST["username"];
    $role = $_POST["role"];
    $status = $_POST["status"];

    $stmt = $conn->prepare("UPDATE USERS SET name=?, email=?, username=?, role=?, status=? WHERE user_id=?");
    $stmt->bind_param("sssssi", $name, $email, $username, $role, $status, $id);
    $stmt->execute();

    header("Location: admin.php");
    exit();
}

// DELETE USER FUNCTION
if (isset($_GET["delete"])) {

    $id = $_GET["delete"];
    $stmt = $conn->prepare("DELETE FROM USERS WHERE user_id=? AND role != 'admin'");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: admin.php");
    exit();
}

// APPROVE USER FUNCTION
if (isset($_GET["approve"])) {

    $id = $_GET["approve"];
    $stmt = $conn->prepare("UPDATE USERS SET status='approved' WHERE user_id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: admin.php");
    exit();
}

// ADD ITEM FUNCTION (FOR CLOTHING)
if (isset($_POST["addItem"])) {
    
    $title = $_POST["title"];
    $description = $_POST["description"];
    $price = $_POST["price"];
    
    $targetDir = "images/";
    
    if(isset($_FILES["itemImage"]) && $_FILES["itemImage"]["error"] == 0) {
        $fileName = basename($_FILES["itemImage"]["name"]);
        $targetFilePath = $targetDir . $fileName;
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
        $allowTypes = array('jpg','png','jpeg','gif');
        
        if(in_array($fileType, $allowTypes)) {
            if(move_uploaded_file($_FILES["itemImage"]["tmp_name"], $targetFilePath)) {
                $imagePath = "images/" . $fileName;
            } else {
                $imagePath = "images/placeholder.jpg";
            }
        } else {
            $imagePath = "images/placeholder.jpg";
        }
    } else {
        $imagePath = "images/placeholder.jpg";
    }

    $stmt = $conn->prepare("INSERT INTO ITEMS (user_id, title, description, price, image, approved) VALUES (1, ?, ?, ?, ?, 1)");
    $stmt->bind_param("ssds", $title, $description, $price, $imagePath);
    $stmt->execute();

    header("Location: admin.php");
    exit();
}

// APPROVE ITEM FUNCTION
if (isset($_GET["approveItem"])) {
    $id = $_GET["approveItem"];
    $conn->query("UPDATE ITEMS SET approved = 1 WHERE item_id = $id");

    header("Location: admin.php");
    exit();
}

// DELETE ITEM FUNCTION
if (isset($_GET["deleteItem"])) {
    $id = $_GET["deleteItem"];
    $conn->query("DELETE FROM ITEMS WHERE item_id = $id");

    header("Location: admin.php");
    exit();
}

// Fetches all users except admin for display
$result = $conn->query("SELECT * FROM USERS WHERE role != 'admin'");
// Fetches all items
$itemsResult = $conn->query("SELECT * FROM ITEMS ORDER BY approved ASC, item_id DESC");
// Fetches all orders
$ordersResult = $conn->query("SELECT * FROM ORDERS ORDER BY order_date DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | Pastimes</title>
    <link rel="stylesheet" href="css/styling.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<div class="admin-page">
    <div class="admin-hero">
        <div class="admin-card">
            
            <div class="admin-heading">
                <div>
                    <p class="admin-small">Administration</p>
                    <h1>Manage Users</h1>
                    <p>Add, edit, approve, and remove users from the platform.</p>
                </div>
                <div>
                    <a href="logout.php" class="admin-logout">Logout</a>
                </div>
            </div>

            <!-- ADD USER FORM -->
            <div class="admin-add-form">
                <input type="text" name="name" placeholder="Full Name" form="addForm" required>
                <input type="email" name="email" placeholder="Email" form="addForm" required>
                <input type="text" name="username" placeholder="Username" form="addForm" required>
                <input type="password" name="password" placeholder="Password" form="addForm" required>
                
                <select name="role" form="addForm">
                    <option value="customer">Customer</option>
                    <option value="seller">Seller</option>
                </select>
                
                <select name="status" form="addForm">
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                </select>

                <button type="submit" name="addUser" form="addForm">Add User</button>
            </div>
            <form id="addForm" method="POST" style="display:none;"></form>

            <!-- USERS TABLE -->
            <div class="admin-table-box">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($user = $result->fetch_assoc()) { ?>
                            <tr>
                                <form method="POST">
                                    <input type="hidden" name="id" value="<?php echo $user['user_id']; ?>">
                                    
                                    <td><input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>"></td>
                                    <td><input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>"></td>
                                    <td><input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>"></td>
                                    
                                    <td>
                                        <select name="role">
                                            <option value="customer" <?php if($user['role'] == 'customer') echo 'selected'; ?>>Customer</option>
                                            <option value="seller" <?php if($user['role'] == 'seller') echo 'selected'; ?>>Seller</option>
                                        </select>
                                    </td>
                                    
                                    <td>
                                        <select name="status">
                                            <option value="pending" <?php if($user['status'] == 'pending') echo 'selected'; ?>>Pending</option>
                                            <option value="approved" <?php if($user['status'] == 'approved') echo 'selected'; ?>>Approved</option>
                                        </select>
                                    </td>

                                    <td class="admin-actions">
                                        <button type="submit" name="updateUser" class="approve-btn">Update</button>
                                        
                                        <?php if($user['status'] == 'pending') { ?>
                                            <a href="admin.php?approve=<?php echo $user['user_id']; ?>" class="approve-btn">Approve</a>
                                        <?php } else { ?>
                                            <span class="approved-text">✓ Approved</span>
                                        <?php } ?>
                                        
                                        <a href="admin.php?delete=<?php echo $user['user_id']; ?>" class="delete-btn" onclick="return confirm('Delete this user?');">Delete</a>
                                    </td>
                                </form>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <!-- MANAGE CLOTHING SECTION -->
            <div style="margin-top: 60px; border-top: 2px solid #ddd; padding-top: 30px;">
                <div class="admin-heading">
                    <div>
                        <h1 style="font-family: Georgia, serif; font-weight: 400;">Manage Clothing Items</h1>
                        <p>Add, view, approve, and delete clothing items from the shop.</p>
                    </div>
                </div>

                <!-- ADD CLOTHING FORM -->
                <div class="admin-add-form" style="grid-template-columns: repeat(3, 1fr); gap: 12px;">
                    <input type="text" name="title" placeholder="Item Title" form="addItemForm" required>
                    <input type="text" name="description" placeholder="Description" form="addItemForm" required>
                    <input type="number" step="0.01" name="price" placeholder="Price" form="addItemForm" required>
                    <input type="file" name="itemImage" accept="image/*" form="addItemForm" required style="padding: 10px; border: 1px solid #ddd; background: white;">
                    <button type="submit" name="addItem" form="addItemForm" style="grid-column: span 3;">Add Item</button>
                </div>
                <form id="addItemForm" method="POST" enctype="multipart/form-data" style="display:none;"></form>

                <!-- CLOTHING TABLE -->
                <div class="admin-table-box">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Title</th>
                                <th>Brand</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($item = $itemsResult->fetch_assoc()) { ?>
                                <tr>
                                    <td><img src="<?php echo $item['image']; ?>" style="width:50px; height:50px; object-fit:cover; border-radius:4px;"></td>
                                    <td><?php echo htmlspecialchars($item['title']); ?></td>
                                    <td><?php echo htmlspecialchars($item['brand']); ?></td>
                                    <td>R<?php echo number_format($item['price'], 2); ?></td>
                                    
                                    <td>
                                        <?php if($item['approved'] == 1) { ?>
                                            <span class="status-badge approved">✓ Approved</span>
                                        <?php } else { ?>
                                            <span class="status-badge pending">Pending</span>
                                        <?php } ?>
                                    </td>

                                    <td class="admin-actions" style="display: flex; gap: 8px;">
                                        <?php if($item['approved'] == 0) { ?>
                                            <a href="admin.php?approveItem=<?php echo $item['item_id']; ?>" class="approve-btn" style="font-size:12px; padding:6px 10px; text-decoration:none;">Approve</a>
                                        <?php } ?>
                                        <a href="admin.php?deleteItem=<?php echo $item['item_id']; ?>" class="delete-btn" onclick="return confirm('Delete this item?');" style="font-size:12px; padding:6px 10px; text-decoration:none;">Delete</a>
                                    </td>
                                </tr>
                            <?php } ?>
                            <?php if($itemsResult->num_rows == 0) { ?>
                                <tr><td colspan="6" style="text-align:center; color:#777;">No clothing items found.</td></tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ORDER MANAGEMENT SECTION -->
            <div style="margin-top: 60px; border-top: 2px solid #ddd; padding-top: 30px;">
                <div class="admin-heading">
                    <div>
                        <h1 style="font-family: Georgia, serif; font-weight: 400;">Order Management</h1>
                        <p>View customer orders and update shipping status.</p>
                    </div>
                </div>

                <!-- UPDATED: Beautiful Message Button here -->
                <div style="text-align: right; margin-bottom: 15px;">
                    <a href="admin-messages.php" style="display: inline-block; background: #2f6b57; color: white; padding: 10px 22px; text-decoration: none; font-weight: bold; border-radius: 4px; font-size: 14px;">
                        <i class="fa-solid fa-envelope" style="margin-right: 8px;"></i> View Buyer/Seller Messages
                    </a>
                </div>

                <div class="admin-table-box">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>User ID</th>
                                <th>Date</th>
                                <th>Delivery Address</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($order = $ordersResult->fetch_assoc()) { ?>
                                <tr>
                                    <td>#<?php echo $order['order_id']; ?></td>
                                    <td>User <?php echo $order['user_id']; ?></td>
                                    <td><?php echo date("F j, Y, g:i a", strtotime($order['order_date'])); ?></td>
                                    <td><?php echo htmlspecialchars($order['delivery_address']); ?></td>
                                    <td>
                                        <?php if($order['status'] == 'shipped') { ?>
                                            <span class="status-badge approved">✓ Shipped</span>
                                        <?php } else { ?>
                                            <span class="status-badge pending">Processing</span>
                                            <br><br>
                                            <a href="update-order-status.php?id=<?php echo $order['order_id']; ?>" class="approve-btn" style="font-size:12px; padding:6px 12px; text-decoration:none;">Mark Shipped</a>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                            <?php if($ordersResult->num_rows == 0) { ?>
                                <tr><td colspan="5" style="text-align:center; color:#777;">No orders have been placed yet.</td></tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>