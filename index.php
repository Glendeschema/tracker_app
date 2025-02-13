<?php
include 'db.php';
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];
$stmt = $pdo->prepare("SELECT * FROM Cycles WHERE user_id = ? ORDER BY start_date DESC LIMIT 5");
$stmt->execute([$user_id]);
$cycles = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container">
    <h2>Welcome, <?= $_SESSION["name"] ?>!</h2>
    <a href="track_cycle.php" class="btn btn-primary">Track Cycle</a>
    <a href="logout.php" class="btn btn-danger">Logout</a>

    <h3>Recent Cycles</h3>
    <ul>
        <?php foreach ($cycles as $cycle) : ?>
            <li><?= $cycle["start_date"] ?> - <?= $cycle["end_date"] ?> (<?= $cycle["flow_intensity"] ?>)</li>
        <?php endforeach; ?>
    </ul>
</body>
</html>