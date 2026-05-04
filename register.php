<?php
// Starts a session so user-related data can be stored while the website is running.
session_start();

// Includes the database connection file.
include "DBConn.php";

// Message variable used to display success or error messages to the user.
$message = "";

// Checks if the form was submitted using the POST method.
// 
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Collects and cleans the form input values.
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);
    $confirmPassword = trim($_POST["confirm_password"]);
    $role = $_POST["role"];

    // Checks whether the password and confirm password fields match.
    if ($password !== $confirmPassword) {
        $message = "Passwords do not match.";
    } else {

        // Hashes the password before saving it into the database.
        // 
        $hashedPassword = md5($password);

        // Checks if the email or username already exists in the USERS table.
        $check = $conn->prepare("SELECT * FROM USERS WHERE email = ? OR username = ?");
        $check->bind_param("ss", $email, $username);
        $check->execute();
        $result = $check->get_result();

        // Prevents duplicate accounts using the same email or username.
        if ($result->num_rows > 0) {
            $message = "Email or username already exists.";
        } else {

            // New users are registered as pending until approved by the administrator.
            // 
            $status = "pending";

            // Inserts the new user into the USERS table.
            // 
            $stmt = $conn->prepare("INSERT INTO USERS (name, email, username, password, role, status) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $name, $email, $username, $hashedPassword, $role, $status);

            // Runs the insert query and checks if registration was successful.
            if ($stmt->execute()) {

                // Gets the ID of the newly registered user.
                $newUserID = $stmt->insert_id;

                // Creates a cart for the new user immediately after registration.
                $cart = $conn->prepare("INSERT INTO CART (user_id) VALUES (?)");
                $cart->bind_param("i", $newUserID);
                $cart->execute();

                // Displays success message after registration.
                $message = "Registration successful. Your account is pending admin approval.";
            } else {
                // Displays error message if registration fails.
                $message = "Registration failed.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Join Pastimes</title>

    <!-- Links the page to the external CSS file for styling. -->
    <link rel="stylesheet" href="css/styling.css">
</head>
<body>

<!-- Main register page container. -->
<div class="register-page">

    <!-- Left side contains the registration form. -->
    <div class="left-side">
        <div class="register-box">
            <h1>Join Pastimes</h1>
            <p>Create your account and start shopping or selling</p>

            <!-- Displays success or error messages to the user. -->
            <?php if($message != ""){ ?>
                <p class="success-message"><?php echo $message; ?></p>
            <?php } ?>

            <!-- Registration form.
             // -->
             
            <form method="POST">
                <label>Full Name</label>
                <input type="text" name="name" required placeholder="John Doe">

                <label>Email</label>
                <input type="email" name="email" required placeholder="your@email.com">

                <label>Username</label>
                <input type="text" name="username" required placeholder="johndoe">

                <label>Password</label>
                <input type="password" name="password" required placeholder="********">

                <label>Confirm Password</label>
                <input type="password" name="confirm_password" required placeholder="********">

                <div class="account-label">I want to</div>

                <!-- Allows user to register as either a customer or seller. -->
                <div class="account-buttons">
                    <button type="submit" name="role" value="customer" class="active">Buy</button>
                    <button type="submit" name="role" value="seller">Sell</button>
                </div>

                <!-- Main create account button. -->
                <button type="submit" name="role" value="customer" class="create-btn">Create Account</button>
            </form>

            <!-- Link to login page for users who already have an account. -->
            <div class="bottom-text">
                Already have an account?
                <a href="login.php">Sign in</a>
            </div>
        </div>
    </div>

    <!-- Right side image section. -->
    <div class="right-side register-image">
        <div class="image-text">
            <h2>"Join our curated community"</h2>
            <p>Connect with verified sellers and quality-conscious buyers.</p>
        </div>
    </div>

</div>

</body>
</html>