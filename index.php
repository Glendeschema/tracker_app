<?php
include 'db.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];

// Handle cycle deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_id'])) {
    $delete_id = $_POST['delete_id'];
    $stmt = $pdo->prepare("DELETE FROM Cycles WHERE id = ? AND user_id = ?");
    $stmt->execute([$delete_id, $user_id]);
    header("Location: index.php");
    exit;
}

// Fetch recent cycles
$stmt = $pdo->prepare("SELECT * FROM Cycles WHERE user_id = ? ORDER BY start_date DESC");
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
    <a href="predicted_cycles.php" class="btn btn-info">View Predicted Cycles</a>
    <a href="logout.php" class="btn btn-danger">Logout</a>

    <h3>Recent Cycles</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($cycles as $cycle) : ?>
                <tr>
                    <td><?= htmlspecialchars($cycle["name"]) ?></td>
                    <td><?= htmlspecialchars($cycle["start_date"]) ?></td>
                    <td><?= htmlspecialchars($cycle["end_date"]) ?></td>
                    <td>
                        <a href="edit_cycle.php?cycle_id=<?= urlencode($cycle['cycle_id']) ?>" class="btn btn-warning btn-sm">Edit</a>


                        <form method="post" style="display:inline;">
                            <input type="hidden" name="delete_id" value="<?= $cycle['cycle_id'] ?>">

                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this cycle?')">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
