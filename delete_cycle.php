<?php
include 'db.php';
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];
$cycle_id = isset($_GET['id']) ? $_GET['id'] : null;

if ($cycle_id) {
    // Delete the cycle from the database
    $stmt = $pdo->prepare("DELETE FROM Cycles WHERE user_id = ? AND cycle_id = ?");
    $stmt->execute([$user_id, $cycle_id]);
}

// Redirect to the dashboard after deletion
header("Location: index.php");
exit;
