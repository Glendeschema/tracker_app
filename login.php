<?php
include 'db.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    // Fetch user from the database
    $stmt = $pdo->prepare("SELECT * FROM Users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die("❌ User not found! Check if the email exists.");
    }

    // Debugging output
    echo "🔹 DB Password Hash: " . $user["password_hash"] . "<br>";
    echo "🔹 Entered Password: " . $password . "<br>";

    // Verify password
    if (password_verify($password, $user["password_hash"])) {
        $_SESSION["user_id"] = $user["user_id"];
        $_SESSION["name"] = $user["name"];

        header("Location: track_cycle.php");
        exit();
    } else {
        die("❌ Incorrect password. Please try again.");
    }
}
?>
