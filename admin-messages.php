<?php
session_start();
include "DBConn.php";

// Mark all unread messages as read because the admin is viewing them
if(isset($_SESSION["user_id"])) {
    $conn->query("UPDATE MESSAGES SET is_read = 1 WHERE receiver_id = " . $_SESSION["user_id"]);
}

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "admin") {
    header("Location: login.php");
    exit();
}

$admin_id = $_SESSION["user_id"];
$message = "";

// HANDLE ADMIN REPLYING TO EXISTING MESSAGE
if (isset($_POST['admin_reply'])) {
    $receiver_id = $_POST['receiver_id']; 
    $msg_subject = "Admin Update: " . trim($_POST['subject']);
    $msg_text = trim($_POST['reply_text']);
    $item_id = $_POST['item_id'];

    $stmt = $conn->prepare("INSERT INTO MESSAGES (sender_id, receiver_id, item_id, subject, message) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iiiss", $admin_id, $receiver_id, $item_id, $msg_subject, $msg_text);
    
    if($stmt->execute()) {
        $message = "✅ Your reply has been sent to the user!";
    } else {
        $message = "❌ Failed to send message.";
    }
}

// HANDLE ADMIN STARTING A BRAND NEW CONVERSATION
if (isset($_POST['admin_compose'])) {
    $receiver_id = $_POST['compose_receiver_id']; 
    $msg_subject = trim($_POST['compose_subject']);
    $msg_text = trim($_POST['compose_message']);
    $item_id = !empty($_POST['compose_item_id']) ? $_POST['compose_item_id'] : NULL;

    $stmt = $conn->prepare("INSERT INTO MESSAGES (sender_id, receiver_id, item_id, subject, message) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iiiss", $admin_id, $receiver_id, $item_id, $msg_subject, $msg_text);
    
    if($stmt->execute()) {
        $message = "✅ Your new message has been sent!";
    } else {
        $message = "❌ Failed to send message.";
    }
}

// Fetch all users (for the dropdown menu)
$allUsers = $conn->query("SELECT user_id, name, role FROM USERS WHERE user_id != $admin_id ORDER BY name ASC");
// Fetch all items (for the dropdown menu)
$allItems = $conn->query("SELECT item_id, title FROM ITEMS ORDER BY title ASC");

// Fetch all messages, ordered newest first
$messages = $conn->query("
    SELECT m.*, 
           s.name as sender_name, 
           r.name as receiver_name, 
           i.title as item_title,
           i.image as item_image
    FROM MESSAGES m
    JOIN USERS s ON m.sender_id = s.user_id
    JOIN USERS r ON m.receiver_id = r.user_id
    LEFT JOIN ITEMS i ON m.item_id = i.item_id
    ORDER BY m.date_sent DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Message Log | Pastimes</title>
    <link rel="stylesheet" href="css/styling.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .msg-container { background: white; border: 1px solid #ddd; border-radius: 8px; padding: 25px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); transition: 0.2s ease; }
        .msg-container:hover { box-shadow: 0 4px 15px rgba(0,0,0,0.08); }
        .msg-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; padding-bottom: 12px; margin-bottom: 12px; }
        .msg-badge { font-size: 12px; font-weight: bold; padding: 4px 12px; border-radius: 20px; }
        .badge-buyer { background: #eaf1ed; color: #2f6b57; }
        .badge-seller { background: #f7e7dc; color: #a65f2f; }
        .msg-body { display: flex; gap: 20px; align-items: flex-start; }
        .msg-body img { width: 60px; height: 60px; object-fit: cover; border-radius: 6px; border: 1px solid #eee; }
        .msg-content { flex: 1; }
        .msg-content h4 { font-size: 16px; margin-bottom: 5px; }
        .msg-content p { color: #555; line-height: 1.6; font-size: 15px; }
        .msg-meta { font-size: 13px; color: #888; margin-top: 8px; }
        .msg-subject { display: inline-block; background: #f8f6f2; padding: 2px 12px; border-radius: 12px; font-size: 13px; color: #333; margin-bottom: 8px; }
        
        .reply-btn { background: #2f6b57; color: white; border: none; padding: 6px 16px; border-radius: 4px; cursor: pointer; font-size: 13px; margin-top: 10px; }
        .reply-btn:hover { background: #24503f; }
        
        .reply-form-container { display: none; margin-top: 15px; background: #f9f9f9; padding: 15px; border-radius: 6px; border: 1px solid #eee; }
        .reply-form-container textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; resize: none; height: 70px; margin-bottom: 10px; font-family: inherit; }
        .reply-form-container button { background: #2f6b57; color: white; border: none; padding: 8px 20px; border-radius: 4px; cursor: pointer; font-weight: bold; }
        .reply-form-container button:hover { background: #1d4234; }

        /* Compose Button Styling */
        .compose-btn-container { text-align: right; margin-bottom: 20px; }
        .compose-btn { background: #2f6b57; color: white; padding: 12px 24px; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; font-size: 15px; }
        .compose-btn:hover { background: #1d4234; }

        /* Compose Modal Styling */
        .modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.4); z-index: 2000; align-items: center; justify-content: center; }
        .modal-box { background: white; width: 90%; max-width: 600px; padding: 30px; border-radius: 8px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
        .modal-box h2 { font-family: Georgia, serif; margin-bottom: 20px; }
        .modal-box select, .modal-box input, .modal-box textarea { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 15px; font-size: 14px; }
        .modal-box textarea { height: 120px; resize: none; }
        .modal-actions { display: flex; gap: 10px; justify-content: flex-end; }
        .modal-actions button { padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; }
        .btn-send { background: #2f6b57; color: white; }
        .btn-cancel { background: #ddd; color: #333; }
    </style>
</head>
<body>

<div class="admin-page">
    <div class="admin-hero">
        <div class="admin-card" style="max-width: 1000px;">
            
            <div class="admin-heading" style="border-bottom: 1px solid #ddd; padding-bottom: 20px; margin-bottom: 30px;">
                <div>
                    <p class="admin-small">Quality Assurance</p>
                    <h1 style="font-family: Georgia, serif; font-weight: 400; font-size: 36px;">Message Log</h1>
                    <p style="color:#777; margin-top: 5px;">Monitor and reply to communications between Sellers and Buyers to ensure item condition and delivery standards are met.</p>
                </div>
                <div>
                    <a href="admin.php" class="admin-logout" style="background:#555; text-decoration:none; padding:10px 20px; color:white; display:inline-block;"><i class="fa-solid fa-arrow-left"></i> Back to Dashboard</a>
                </div>
            </div>

            <?php if($message != "") { ?>
                <div style="background: white; border: 1px solid #ddd; padding: 15px; margin-bottom: 20px; color: #2f6b57; font-weight: bold;">
                    <?php echo $message; ?>
                </div>
            <?php } ?>

            <!-- COMPOSE NEW MESSAGE BUTTON -->
            <div class="compose-btn-container">
                <button class="compose-btn" onclick="openComposeModal()">
                    <i class="fa-solid fa-pen-to-square"></i> Compose New Message
                </button>
            </div>

            <!-- COMPOSE MODAL (Hidden by default) -->
            <div class="modal-overlay" id="composeModal">
                <div class="modal-box">
                    <h2>✉️ Compose Message</h2>
                    <form method="POST">
                        <label style="font-weight: bold; display: block; margin-bottom: 5px;">Send To (Buyer/Seller)</label>
                        <select name="compose_receiver_id" required>
                            <option value="">Select a user...</option>
                            <?php while($u = $allUsers->fetch_assoc()) { ?>
                                <option value="<?php echo $u['user_id']; ?>">
                                    <?php echo ucfirst($u['role']); ?>: <?php echo htmlspecialchars($u['name']); ?>
                                </option>
                            <?php } ?>
                        </select>

                        <label style="font-weight: bold; display: block; margin-bottom: 5px;">Related Item (Optional)</label>
                        <select name="compose_item_id">
                            <option value="">No specific item</option>
                            <?php 
                            // Reset pointer because we used it above
                            $allItems->data_seek(0); 
                            while($i = $allItems->fetch_assoc()) { ?>
                                <option value="<?php echo $i['item_id']; ?>">
                                    <?php echo htmlspecialchars($i['title']); ?>
                                </option>
                            <?php } ?>
                        </select>

                        <label style="font-weight: bold; display: block; margin-bottom: 5px;">Subject</label>
                        <input type="text" name="compose_subject" placeholder="e.g. Delivery Confirmation" required>

                        <label style="font-weight: bold; display: block; margin-bottom: 5px;">Message</label>
                        <textarea name="compose_message" placeholder="Write your message here..." required></textarea>

                        <div class="modal-actions">
                            <button type="button" class="btn-cancel" onclick="closeComposeModal()">Cancel</button>
                            <button type="submit" name="admin_compose" class="btn-send">Send Message</button>
                        </div>
                    </form>
                </div>
            </div>

            <?php if($messages->num_rows == 0) { ?>
                <div style="text-align: center; padding: 60px 20px; color: #777;">
                    <i class="fa-solid fa-inbox" style="font-size: 40px; color: #ddd; margin-bottom: 15px;"></i>
                    <p>No messages have been exchanged yet.</p>
                </div>
            <?php } else { ?>

                <div style="max-height: 600px; overflow-y: auto; padding-right: 5px;">
                    <?php while($row = $messages->fetch_assoc()) { ?>
                        
                        <div class="msg-container">
                            <div class="msg-header">
                                <div>
                                    <span class="msg-badge badge-buyer"><i class="fa-solid fa-user"></i> <?php echo ucfirst($row['sender_name']); ?></span>
                                    <span style="color:#aaa; margin:0 8px;">→</span>
                                    <span class="msg-badge badge-seller"><i class="fa-solid fa-store"></i> <?php echo ucfirst($row['receiver_name']); ?></span>
                                </div>
                                <span style="font-size:13px; color:#888;">
                                    <?php echo date("M j, Y • g:i a", strtotime($row['date_sent'])); ?>
                                </span>
                            </div>

                            <div class="msg-body">
                                <?php if($row['item_image']) { ?>
                                    <img src="<?php echo htmlspecialchars($row['item_image']); ?>" alt="Item Image">
                                <?php } else { ?>
                                    <div style="width:60px; height:60px; background:#f0f0f0; border-radius:6px; display:flex; align-items:center; justify-content:center; color:#bbb; font-size:20px;">
                                        <i class="fa-solid fa-shirt"></i>
                                    </div>
                                <?php } ?>
                                
                                <div class="msg-content">
                                    <div>
                                        <span class="msg-subject"><?php echo htmlspecialchars($row['subject']); ?></span>
                                        <?php if($row['item_title']) { ?>
                                            <span style="font-size:13px; color:#2f6b57; margin-left:8px;">
                                                <i class="fa-solid fa-tag"></i> <?php echo htmlspecialchars($row['item_title']); ?>
                                            </span>
                                        <?php } ?>
                                    </div>
                                    <p><?php echo nl2br(htmlspecialchars($row['message'])); ?></p>
                                    
                                    <!-- ADMIN REPLY BUTTON -->
                                    <button class="reply-btn" onclick="toggleReplyForm(<?php echo $row['message_id']; ?>)">
                                        <i class="fa-solid fa-reply"></i> Reply to User
                                    </button>

                                    <!-- ADMIN REPLY FORM -->
                                    <div class="reply-form-container" id="replyForm_<?php echo $row['message_id']; ?>">
                                        <form method="POST">
                                            <input type="hidden" name="receiver_id" value="<?php echo $row['sender_id']; ?>">
                                            <input type="hidden" name="item_id" value="<?php echo $row['item_id']; ?>">
                                            <input type="hidden" name="subject" value="Regarding your conversation about <?php echo htmlspecialchars($row['item_title']); ?>">
                                            
                                            <textarea name="reply_text" placeholder="Type your quality assurance message here..." required></textarea>
                                            
                                            <button type="submit" name="admin_reply">Send Message</button>
                                            <button type="button" onclick="toggleReplyForm(<?php echo $row['message_id']; ?>)" style="background:#ddd; color:#333; border:none; padding:8px 20px; border-radius:4px; cursor:pointer; margin-left:5px;">Cancel</button>
                                        </form>
                                    </div>

                                </div>
                            </div>
                        </div>

                    <?php } ?>
                </div>

            <?php } ?>

        </div>
    </div>
</div>

<!-- JavaScript to handle the Modals -->
<script>
    function toggleReplyForm(id) {
        var form = document.getElementById('replyForm_' + id);
        if (form.style.display === 'none' || form.style.display === '') {
            form.style.display = 'block';
        } else {
            form.style.display = 'none';
        }
    }

    function openComposeModal() {
        document.getElementById('composeModal').style.display = 'flex';
    }

    function closeComposeModal() {
        document.getElementById('composeModal').style.display = 'none';
    }
    
    // Close modal if user clicks outside the box
    window.onclick = function(event) {
        var modal = document.getElementById('composeModal');
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    }
</script>

</body>
</html>