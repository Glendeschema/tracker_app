<?php
include 'db.php';
session_start();

// Ensure user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];
$cycle_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Debugging: Show the received ID
if (!$cycle_id) {
    die("❌ Error: No cycle ID provided in URL! <br> Debug Info: <pre>" . print_r($_GET, true) . "</pre><a href='index.php'>Go Back</a>");
}

echo "✅ Cycle ID received: " . htmlspecialchars($cycle_id) . "<br>";

// Fetch cycle details
$stmt = $pdo->prepare("SELECT * FROM Cycles WHERE id = ? AND user_id = ?");
$stmt->execute([$cycle_id, $user_id]);
$cycle = $stmt->fetch();

// Debugging: Show if cycle was found
if (!$cycle) {
    die("❌ Error: Cycle not found in database! <a href='index.php'>Go Back</a>");
} else {
    echo "✅ Cycle found: " . htmlspecialchars($cycle['name']) . "<br>";
}
?>
