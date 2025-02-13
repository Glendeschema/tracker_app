<?php
include 'db.php';
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];

// Fetch the user's recent cycles to predict the next cycle
$stmt = $pdo->prepare("SELECT * FROM Cycles WHERE user_id = ? ORDER BY start_date DESC LIMIT 5");
$stmt->execute([$user_id]);
$cycles = $stmt->fetchAll();

// Function to predict the next cycle based on the most recent cycle
function predictNextCycle($lastCycleStartDate, $lastCycleEndDate) {
    // Predict next cycle by adding the average cycle length (28 days) and the average period duration (5 days)
    $cycleLength = 28; // average cycle length in days
    $periodDuration = 5; // average period duration in days

    // Convert last cycle's end date to DateTime object
    $lastEndDate = new DateTime($lastCycleEndDate);
    $lastEndDate->add(new DateInterval('P' . ($cycleLength - $periodDuration) . 'D')); // Add cycle length minus period duration
    $predictedStartDate = $lastEndDate->format('Y-m-d');

    $lastEndDate->add(new DateInterval('P' . $periodDuration . 'D')); // Add the period duration
    $predictedEndDate = $lastEndDate->format('Y-m-d');

    return [$predictedStartDate, $predictedEndDate];
}

$predictions = [];

foreach ($cycles as $cycle) {
    // For each cycle, predict the next one
    $predictedDates = predictNextCycle($cycle["start_date"], $cycle["end_date"]);
    $predictions[] = [
        'name' => $cycle["name"], // Add the cycle name to predictions
        'predicted_start' => $predictedDates[0],
        'predicted_end' => $predictedDates[1]
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Predicted Cycles</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container">
    <h2>Predicted Next Cycles</h2>
    <a href="index.php" class="btn btn-primary">Back to Dashboard</a>

    <h3>Recent Cycles and Their Predicted Next Cycles</h3>
    <ul>
        <?php foreach ($predictions as $prediction) : ?>
            <li>
                <strong><?= $prediction['name'] ?></strong> (<?= $prediction['predicted_start'] ?> - <?= $prediction['predicted_end'] ?>)
            </li> <!-- Display cycle name and predicted dates -->
        <?php endforeach; ?>
    </ul>
</body>
</html>
