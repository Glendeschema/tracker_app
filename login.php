<?php
// Enable error reporting to see if anything is wrong
ini_set('display_errors', 1);
error_reporting(E_ALL);

include 'db.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    $stmt = $pdo->prepare("SELECT * FROM Users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die("User not found!");
    }

    if (password_verify($password, $user["password_hash"])) {
        $_SESSION["user_id"] = $user["user_id"];
        $_SESSION["name"] = $user["name"];
        header("Location: track_cycle.php");
        exit();
    } else {
        die("Incorrect password. Please try again.");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <h2>Login</h2>
    <form method="post">
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <button type="submit">Login</button>
    </form>
</body>
</html>
