<?php
include 'db.php';
session_start();

// Ensure user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];

// Fetch all cycles grouped by name
$stmt = $pdo->prepare("SELECT DISTINCT name FROM Cycles WHERE user_id = ?");
$stmt->execute([$user_id]);
$names = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Fetch all cycles to generate predictions
$stmt = $pdo->prepare("SELECT * FROM Cycles WHERE user_id = ? ORDER BY start_date DESC");
$stmt->execute([$user_id]);
$cycles = $stmt->fetchAll();

// Function to predict next cycles
function predictNextCycle($lastCycleStartDate, $lastCycleEndDate) {
    $cycleLength = 28; // Average cycle length
    $periodDuration = 5; // Average period duration

    $lastEndDate = new DateTime($lastCycleEndDate);
    $lastEndDate->add(new DateInterval('P' . ($cycleLength - $periodDuration) . 'D'));
    $predictedStartDate = $lastEndDate->format('Y-m-d');

    $lastEndDate->add(new DateInterval('P' . $periodDuration . 'D'));
    $predictedEndDate = $lastEndDate->format('Y-m-d');

    return [$predictedStartDate, $predictedEndDate];
}

// Group predictions by name
$predictions = [];
foreach ($cycles as $cycle) {
    $predictedDates = predictNextCycle($cycle["start_date"], $cycle["end_date"]);
    $predictions[$cycle["name"]][] = [
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
    <script>
        function filterNames() {
            let searchValue = document.getElementById("searchInput").value.toLowerCase();
            let nameSections = document.querySelectorAll(".name-section");

            nameSections.forEach(section => {
                let name = section.getAttribute("data-name").toLowerCase();
                section.style.display = name.includes(searchValue) ? "block" : "none";
            });
        }
    </script>
</head>
<body class="container">
    <h2>Predicted Next Cycles</h2>
    <a href="index.php" class="btn btn-primary mb-3">Back to Dashboard</a>

    <!-- Search Bar -->
    <div class="mb-3">
        <input type="text" id="searchInput" class="form-control" placeholder="Search by name..." onkeyup="filterNames()">
    </div>

    <!-- Display Predictions -->
    <?php foreach ($predictions as $name => $cycles) : ?>
        <div class="name-section card mb-3 p-3" data-name="<?= htmlspecialchars($name) ?>">
            <h4><?= htmlspecialchars($name) ?></h4>
            <ul>
                <?php foreach ($cycles as $prediction) : ?>
                    <li>
                        Predicted: <strong><?= $prediction['predicted_start'] ?></strong> to <strong><?= $prediction['predicted_end'] ?></strong>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endforeach; ?>
</body>
</html>
