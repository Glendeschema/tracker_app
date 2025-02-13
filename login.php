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
        die("<div class='alert alert-danger' role='alert'>User not found!</div>");
    }

    if (password_verify($password, $user["password_hash"])) {
        $_SESSION["user_id"] = $user["user_id"];
        $_SESSION["name"] = $user["name"];
        header("Location: index.php");
        exit();
    } else {
        die("<div class='alert alert-danger' role='alert'>Incorrect password. Please try again.</div>");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #333;
            font-family: 'Arial', sans-serif;
            color: #fff;
        }
        .container {
            max-width: 500px;
            padding: 40px;
            margin-top: 80px;
            background-color: #444;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.5);
        }
        h2 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 32px;
            font-weight: bold;
            color: #ff9900;
        }
        .catchy-phrase {
            text-align: center;
            font-size: 48px;
            font-weight: bold;
            color: #ff5733;
            margin-bottom: 30px;
        }
        .slogan {
            text-align: center;
            margin-top: 15px;
            font-size: 18px;
            font-style: italic;
            color: #c2c2c2;
        }
        input[type="email"], input[type="password"] {
            background-color: #333;
            color: #fff;
            border: 2px s
