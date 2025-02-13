<?php
include 'db.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];

// Default: show 3 months of predictions
$months_to_predict = isset($_GET['months']) ? (int)$_GET['months'] : 3;

// Fetch the user's recent cycles to predict the next cycle
$stmt = $pdo->prepare("SELECT TOP 5 * FROM Cycles WHERE user_id = ? ORDER BY start_date DESC");
$stmt->execute([$user_id]);
$cycles = $stmt->fetchAll();

// Function to predict the next cycle based on the most recent cycle
function predictNextCycle($lastCycleStartDate, $lastCycleEndDate, $months_to_predict) {
    // Predict next cycle by adding the average cycle length (28 days) and the average period duration (5 days)
    $cycleLength = 28; // average cycle length in days
    $periodDuration = 5; // average period duration in days

    $predictions = [];
    
    // Calculate the number of days to predict based on months selected
    $daysToPredict = $months_to_predict * 30; // Roughly calculate the number of days in the given months

    $lastEndDate = new DateTime($lastCycleEndDate);
    for ($i = 0; $i < $months_to_predict; $i++) {
        $lastEndDate->add(new DateInterval('P' . ($cycleLength - $periodDuration) . 'D')); // Add cycle length minus period duration
        $predictedStartDate = $lastEndDate->format('Y-m-d');

        $lastEndDate->add(new DateInterval('P' . $periodDuration . 'D')); // Add the period duration
        $predictedEndDate = $lastEndDate->format('Y-m-d');
        
        $predictions[] = ['predicted_start' => $predictedStartDate, 'predicted_end' => $predictedEndDate];
    }

    return $predictions;
}

$predictions = [];

// Process each cycle and predict the next ones
foreach ($cycles as $cycle) {
    $predictedCycles = predictNextCycle($cycle["start_date"], $cycle["end_date"], $months_to_predict);
    
    // Group predictions by name
    $predictions[$cycle["name"]] = $predictedCycles;
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
    
    <form method="get" action="predicted_cycles.php">
        <label for="months">Select months to predict:</label>
        <select name="months" id="months" class="form-control" onchange="this.form.submit()">
            <option value="1" <?= $months_to_predict == 1 ? 'selected' : '' ?>>1 Month</option>
            <option value="2" <?= $months_to_predict == 2 ? 'selected' : '' ?>>2 Months</option>
            <option value="3" <?= $months_to_predict == 3 ? 'selected' : '' ?>>3 Months</option>
            <option value="6" <?= $months_to_predict == 6 ? 'selected' : '' ?>>6 Months</option>
            <option value="12" <?= $months_to_predict == 12 ? 'selected' : '' ?>>12 Months</option>
        </select>
    </form>

    <a href="index.php" class="btn btn-primary mt-3">Back to Dashboard</a>

    <h3>Predicted Cycles</h3>
    <?php if (count($predictions) > 0) : ?>
        <?php foreach ($predictions as $name => $predictedCycles) : ?>
            <h4><?= htmlspecialchars($name) ?>'s Predicted Cycles</h4>
            <ul>
                <?php foreach ($predictedCycles as $prediction) : ?>
                    <li>
                        <?= $prediction['predicted_start'] ?> - <?= $prediction['predicted_end'] ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endforeach; ?>
    <?php else : ?>
        <p>No cycles found for prediction.</p>
    <?php endif; ?>
</body>
</html>
