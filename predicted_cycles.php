<?php
include 'db.php';
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];

// Fetch the latest cycle for the user
$stmt = $pdo->prepare("SELECT TOP 1 * FROM Cycles WHERE user_id = ? ORDER BY start_date DESC");
$stmt->execute([$user_id]);
$cycle = $stmt->fetch();

// Default prediction to 28-day cycle if no cycles are recorded
$predicted_cycle_length = 28; // Default to 28 days (you can change this logic based on your needs)

if ($cycle) {
    $last_cycle_end = new DateTime($cycle['end_date']);
    
    // Calculate the next cycles based on the most recent cycle
    $predicted_cycles = [];
    for ($i = 1; $i <= 6; $i++) {  // Generate predictions for the next 6 cycles (next 6 months)
        $next_cycle_start = $last_cycle_end->add(new DateInterval('P' . $predicted_cycle_length . 'D')); // Add cycle length to start date
        $next_cycle_end = clone $next_cycle_start;
        $next_cycle_end->add(new DateInterval('P' . $predicted_cycle_length . 'D'));  // Add cycle length for the end date
        
        // Store predicted cycle details
        $predicted_cycles[] = [
            'start_date' => $next_cycle_start->format('Y-m-d'),
            'end_date' => $next_cycle_end->format('Y-m-d'),
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Predicted Cycles</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container">
    <h2>Predicted Cycles</h2>
    <a href="index.php" class="btn btn-primary">Back to Dashboard</a>
    <h3>Upcoming Predicted Cycles</h3>
    
    <?php if (isset($predicted_cycles) && count($predicted_cycles) > 0) : ?>
        <ul class="list-group">
            <?php foreach ($predicted_cycles as $index => $predicted_cycle) : ?>
                <li class="list-group-item">
                    <strong>Predicted Cycle <?= $index + 1 ?>:</strong>
                    <br>Start Date: <?= $predicted_cycle['start_date'] ?>
                    <br>End Date: <?= $predicted_cycle['end_date'] ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No cycles data available to predict. Please track a cycle first.</p>
    <?php endif; ?>
</body>
</html>
