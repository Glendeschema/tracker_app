<?php
include 'db.php';
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];
$stmt = $pdo->prepare("SELECT TOP 5 * FROM Cycles WHERE user_id = ? ORDER BY start_date DESC");
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
            <?php
                // Calculate cycle length (difference between start and end dates)
                $start_date = new DateTime($cycle['start_date']);
                $end_date = new DateTime($cycle['end_date']);
                $cycle_length = $start_date->diff($end_date)->days; // Cycle length in days

                // Predict next cycle start and end dates
                $last_cycle_end = new DateTime($cycle['end_date']);
                $next_cycle_start = $last_cycle_end->add(new DateInterval('P' . $cycle_length . 'D'));  // Add cycle length in days
                $next_cycle_end = clone $next_cycle_start;
                $next_cycle_end->add(new DateInterval('P' . $cycle_length . 'D'));  // Add cycle length again for end date
            ?>

            <li>
                <?= $cycle["start_date"] ?> - <?= $cycle["end_date"] ?> (<?= $cycle["flow_intensity"] ?>)<br>
                <strong>Predicted Next Cycle:</strong> <?= $next_cycle_start->format('Y-m-d') ?> to <?= $next_cycle_end->format('Y-m-d') ?>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
