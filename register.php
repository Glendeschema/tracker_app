<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    // Hash the password before storing
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO Users (name, email, password_hash) VALUES (?, ?, ?)");
    $stmt->execute([$name, $email, $hashedPassword]);

    echo "Registration successful! You can now login.";
}
?>
