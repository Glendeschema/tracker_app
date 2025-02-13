<?php
include 'db.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];

try {
    // Fetch the top 5 most recent cycles for the user
    $stmt = $pdo->prepare("SELECT TOP 5 * FROM Cycles WHERE user_id = ? ORDER BY start_date DESC");
    $stmt->execute([$user_id]);
    $cycles = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error fetching cycles: " . $e->getMessage());
}

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
    <?php if (!empty($cycles)): ?>
        <ul>
            <?php foreach ($cycles as $cycle) : ?>
                <li><?= $cycle["start_date"] ?> - <?= $cycle["end_date"] ?> (<?= $cycle["flow_intensity"] ?>)</li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No cycles logged yet.</p>
    <?php endif; ?>
</body>
</html>
