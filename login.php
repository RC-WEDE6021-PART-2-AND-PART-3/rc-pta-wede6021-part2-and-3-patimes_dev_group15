<?php
// Enables error reporting for debugging purposes.
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Starts a session to store user login information.
session_start();

// Includes database connection.
include "DBConn.php";

// Variable to store error messages.
$error = "";

// Variable to keep the entered username after submission.
$username = "";

// Checks if the login form was submitted.
// 
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Gets user input and trims extra spaces.
    $username = trim($_POST["username"]);

    // Hashes the password before comparing with database.
    // 
    $password = md5(trim($_POST["password"]));

    // Prepares SQL query to check if username and password match a record.
    // 
    $stmt = $conn->prepare("SELECT * FROM USERS WHERE username = ? AND password = ?");

    // Checks if SQL preparation failed.
    if (!$stmt) {
        die("SQL prepare error: " . $conn->error);
    }

    // Binds the input values to the SQL query.
    $stmt->bind_param("ss", $username, $password);

    // Executes the query.
    $stmt->execute();

    // Gets the result.
    $result = $stmt->get_result();

    // Checks if exactly one user was found.
    if ($result->num_rows == 1) {

        // Fetches the user data.
        $user = $result->fetch_assoc();

        // Checks if user is approved by admin.
        // 
        if ($user["status"] != "approved") {
            $error = "Your account is pending admin approval.";
        } else {

            // Stores user data in session variables.
            // 
            $_SESSION["user_id"] = $user["user_id"];
            $_SESSION["name"] = $user["name"];
            $_SESSION["role"] = $user["role"];

            // Redirects admin users to admin dashboard.
            if ($user["role"] == "admin") {
                header("Location: admin.php");
                exit();
            } 
            // Redirects normal users to homepage.
            else {
                header("Location: index.php");
                exit();
            }
        }
    } else {
        // Displays error if login fails.
        // 
        $error = "Incorrect username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | Pastimes</title>

    <!-- Links to external CSS file -->
    <link rel="stylesheet" href="css/styling.css">
</head>
<body>

<div class="login-page">

    <!-- Left side: login form -->
    <div class="left-side">
        <div class="login-box">

            <h1>Welcome back</h1>
            <p>Sign in to your Pastimes account</p>

            <!-- Displays error messages -->
            <?php if($error != ""){ ?>
                <p style="color:red; margin-bottom:15px;">
                    <?php echo $error; ?>
                </p>
            <?php } ?>

            <!-- Login form -->
            <form method="POST" action="login.php">

                <label>Username</label>
                <!-- required prevents empty submission -->
                <input type="text" name="username" required 
                       value="<?php echo htmlspecialchars($username); ?>" 
                       placeholder="admin">

                <label>Password</label>
                <input type="password" name="password" required placeholder="admin123">

                <button type="submit" class="login-btn">Sign In</button>

            </form>

            <!-- Link to registration page -->
            <div class="bottom-text">
                Don’t have an account?
                <a href="register.php">Create one</a>
            </div>

        </div>
    </div>

    <!-- Right side image -->
    <div class="right-side login-image">
        <div class="image-text">
            <h2>"Quality never goes out of style"</h2>
        </div>
    </div>

</div>

</body>
</html>