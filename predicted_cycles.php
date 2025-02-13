<?php
include 'db.php';
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];
$filter_name = $_GET['name'] ?? '';  // Get name filter

// Fetch all user's recent cycles
$query = "SELECT * FROM Cycles WHERE user_id = ? ";
$params = [$user_id];

if (!empty($filter_name)) {
    $query .= "AND name = ? ";
    $params[] = $filter_name;
}

$query .= "ORDER BY start_date DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$cycles = $stmt->fetchAll();

// Function to predict cycles for the next N months
function predictNextCycles($lastStartDate, $lastEndDate, $months) {
    $cycleLength = 28; // Average cycle length
    $periodDuration = 6; // Days of period duration
    $predictions = [];

    $lastStart = new DateTime($lastStartDate);
    $lastEnd = new DateTime($lastEndDate);

    for ($i = 1; $i <= $months; $i++) {
        $lastStart->add(new DateInterval("P{$cycleLength}D"));
        $predictedStart = $lastStart->format('Y-m-d');

        $predictedEnd = (clone $lastStart)->add(new DateInterval("P{$periodDuration}D"))->format('Y-m-d');

        $predictions[] = ['start' => $predictedStart, 'end' => $predictedEnd];
    }

    return $predictions;
}

$predictions = [];

// Group predictions by name
foreach ($cycles as $cycle) {
    $predictions[$cycle["name"]][] = $cycle;
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

    <form method="get" class="mb-3">
        <label>Filter by Name:</label>
        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($filter_name) ?>" placeholder="Enter name">

        <button type="submit" class="btn btn-info mt-2">Filter</button>
    </form>

    <h3>Predictions</h3>
    <?php if (empty($predictions)) : ?>
        <p>No predictions found.</p>
    <?php else : ?>
        <?php foreach ($predictions as $name => $cycleList) : ?>
            <h4><?= htmlspecialchars($name) ?></h4>
            <form method="get">
                <label>Predict for (months) for <?= htmlspecialchars($name) ?>:</label>
                <input type="hidden" name="name" value="<?= htmlspecialchars($name) ?>">
                <select name="months" class="form-control mb-2">
                    <option value="1">1 Month</option>
                    <option value="3">3 Months</option>
                    <option value="6">6 Months</option>
                </select>
                <button type="submit" class="btn btn-primary mb-3">Show Predictions</button>
            </form>
            <ul>
                <?php
                // Default to 3 months if no filter is set
                $months = $_GET['months'] ?? 3;

                // Predict for the selected number of months
                foreach ($cycleList as $cycle) {
                    $predictedDates = predictNextCycles($cycle["start_date"], $cycle["end_date"], $months);
                    foreach ($predictedDates as $dates) {
                        echo "<li>{$dates['start']} to {$dates['end']}</li>";
                    }
                }
                ?>
            </ul>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
