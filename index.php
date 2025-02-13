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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f4f7f6;
        }
        .container {
            max-width: 960px;
            margin-top: 30px;
        }
        .header {
            background-color: #007bff;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
        }
        .dashboard-card {
            margin-top: 20px;
            border-radius: 10px;
            background-color: #ffffff;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }
        .cycle-item {
            border-bottom: 1px solid #e0e0e0;
            padding: 10px;
        }
        .btn-primary {
            background-color: #28a745;
            border-color: #28a745;
        }
        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header text-center">
            <h2>Welcome, <?= $_SESSION["name"] ?>!</h2>
            <p>Let's track your cycles and stay on top of your health</p>
        </div>
        
        <div class="d-flex justify-content-between">
            <a href="track_cycle.php" class="btn btn-primary">Track Cycle</a>
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>

        <div class="dashboard-card mt-4">
            <div class="card-body">
                <h3>Recent Cycles</h3>
                <?php if (!empty($cycles)): ?>
                    <ul class="list-group">
                        <?php foreach ($cycles as $cycle): ?>
                            <li class="list-group-item cycle-item">
                                <strong>Start Date:</strong> <?= $cycle["start_date"] ?><br>
                                <strong>End Date:</strong> <?= $cycle["end_date"] ?><br>
                                <strong>Flow Intensity:</strong> <?= $cycle["flow_intensity"] ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>No cycles logged yet. Start tracking your cycle today!</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
