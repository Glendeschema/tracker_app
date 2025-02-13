<?php
include 'db.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];

// Fetch the user's recent cycles to predict the next cycle
$stmt = $pdo->prepare("SELECT * FROM Cycles WHERE user_id = ? ORDER BY start_date DESC");
$stmt->execute([$user_id]);
$cycles = $stmt->fetchAll();

// Function to predict the next cycle based on the most recent cycle
function predictNextCycle($lastCycleStartDate, $lastCycleEndDate, $months_to_predict) {
    $cycleLength = 28; // average cycle length in days
    $periodDuration = 5; // average period duration in days

    $predictions = [];
    $lastEndDate = new DateTime($lastCycleEndDate);

    for ($i = 0; $i < $months_to_predict; $i++) {
        $lastEndDate->add(new DateInterval('P' . ($cycleLength - $periodDuration) . 'D'));
        $predictedStartDate = $lastEndDate->format('Y-m-d');

        $lastEndDate->add(new DateInterval('P' . $periodDuration . 'D'));
        $predictedEndDate = $lastEndDate->format('Y-m-d');
        
        $predictions[] = ['predicted_start' => $predictedStartDate, 'predicted_end' => $predictedEndDate];
    }

    return $predictions;
}

$predictions = [];
$months_filter = []; // Store the filter months for each cycle name

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle the form submission
    $months_filter = $_POST['months_filter'] ?? [];
}

foreach ($cycles as $cycle) {
    $selected_months = isset($months_filter[$cycle["name"]]) ? (int)$months_filter[$cycle["name"]] : 3;
    $predictedCycles = predictNextCycle($cycle["start_date"], $cycle["end_date"], $selected_months);
    
    // Group predictions by name
    if (!isset($predictions[$cycle["name"]])) {
        $predictions[$cycle["name"]] = [];
    }

    foreach ($predictedCycles as $predictedCycle) {
        $predictions[$cycle["name"]][] = $predictedCycle;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Predicted Cycles</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .card {
            margin-bottom: 20px;
        }
        .cycle-name {
            font-size: 1.5em;
            font-weight: bold;
        }
        .cycle-prediction {
            font-size: 1.2em;
            margin: 5px 0;
        }
        .prediction-form {
            margin-bottom: 15px;
        }
    </style>
</head>
<body class="container">
    <h2 class="my-4">Predicted Next Cycles</h2>
    <a href="index.php" class="btn btn-primary mb-4">Back to Dashboard</a>

    <form method="post" action="predicted_cycles.php" class="mb-4">
        <?php foreach ($predictions as $name => $cyclePredictions) : ?>
            <div class="card">
                <div class="card-header">
                    <div class="cycle-name"><?= htmlspecialchars($name) ?>'s Predicted Cycles</div>
                </div>
                <div class="card-body">
                    <div class="prediction-form">
                        <label for="months_<?= htmlspecialchars($name) ?>" class="form-label">Select months to predict for <?= htmlspecialchars($name) ?>:</label>
                        <select name="months_filter[<?= htmlspecialchars($name) ?>]" id="months_<?= htmlspecialchars($name) ?>" class="form-select" onchange="this.form.submit()">
                            <option value="1" <?= (isset($months_filter[$name]) && $months_filter[$name] == 1) ? 'selected' : '' ?>>1 Month</option>
                            <option value="2" <?= (isset($months_filter[$name]) && $months_filter[$name] == 2) ? 'selected' : '' ?>>2 Months</option>
                            <option value="3" <?= (isset($months_filter[$name]) && $months_filter[$name] == 3) ? 'selected' : '' ?>>3 Months</option>
                            <option value="6" <?= (isset($months_filter[$name]) && $months_filter[$name] == 6) ? 'selected' : '' ?>>6 Months</option>
                            <option value="12" <?= (isset($months_filter[$name]) && $months_filter[$name] == 12) ? 'selected' : '' ?>>12 Months</option>
                        </select>
                    </div>

                    <ul>
                        <?php foreach ($cyclePredictions as $prediction) : ?>
                            <li class="cycle-prediction">
                                <?= $prediction['predicted_start'] ?> - <?= $prediction['predicted_end'] ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endforeach; ?>
    </form>

</body>
</html>
