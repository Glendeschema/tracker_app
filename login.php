<?php
include 'db.php'; // Ensure db.php properly sets up $pdo
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    // Debug: Check if email is being received correctly
    if (empty($email) || empty($password)) {
        echo "Please enter both email and password.";
        exit();
    }

    // Fetch user from database
    $stmt = $pdo->prepare("SELECT * FROM Users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Debug: Check if user is retrieved
    if (!$user) {
        echo "User not found. Please check your email.";
        exit();
    }

    // Verify password
    if (password_verify($password, $user["password_hash"])) {
        // Store user data in session
        $_SESSION["user_id"] = $user["user_id"];
        $_SESSION["name"] = $user["name"];

        // Redirect to track_cycle.php
        header("Location: index.php");
        exit();
    } else {
        echo "Incorrect password. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container">
    <h2>Login</h2>
    <form method="post">
        <input type="email" name="email" class="form-control" placeholder="Email" required><br>
        <input type="password" name="password" class="form-control" placeholder="Password" required><br>
        <button type="submit" class="btn btn-success">Login</button>
    </form>
</body>
</html>
