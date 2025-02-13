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
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }
        .container {
            max-width: 500px;
            padding: 30px;
            margin-top: 50px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 30px;
        }
        .catchy-phrase {
            text-align: center;
            margin-top: 20px;
            font-size: 18px;
            font-weight: bold;
            color: #28a745;
        }
        .slogan {
            text-align: center;
            margin-top: 10px;
            font-size: 16px;
            color: #6c757d;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Login</h2>
        <form method="post">
            <div class="mb-3">
                <input type="email" name="email" class="form-control" placeholder="Email" required>
            </div>
            <div class="mb-3">
                <input type="password" name="password" class="form-control" placeholder="Password" required>
            </div>
            <button type="submit" class="btn btn-success w-100">Login</button>
        </form>
        <p class="mt-3 text-center">Don't have an account? <a href="register.php">Sign up here</a></p>
        
        <!-- Catchy Phrase and Slogan -->
        <div class="catchy-phrase">
            <p>Man Cave APP</p>
        </div>
        <div class="slogan">
            <p>Never feed a cow that doesn't feed you grass!</p>
        </div>
    </div>

</body>
</html>
