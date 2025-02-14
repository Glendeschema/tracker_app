<?php
include 'db.php';
session_start();

// Ensure the user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];

// Check if delete_id is provided in the POST request
if (!isset($_POST['delete_id'])) {
    // If no cycle ID is provided, redirect back to the dashboard
    header("Location: index.php");
    exit;
}

$delete_id = $_POST['delete_id'];

// Delete the cycle from the database
$stmt = $pdo->prepare("DELETE FROM Cycles WHERE user_id = ? AND cycle_id = ?");
$stmt->execute([$user_id, $delete_id]);

// After deleting the cycle, redirect back to the index page
header("Location: index.php");
exit;
?>
