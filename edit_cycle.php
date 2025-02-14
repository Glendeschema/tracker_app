<?php
include 'db.php';
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];

// Check if cycle_id is provided for editing
if (!isset($_GET['cycle_id'])) {
    header("Location: index.php");
    exit;
}

$cycle_id = $_GET['cycle_id'];

// Get the cycle details to pre-fill the form
$stmt = $pdo->prepare("SELECT * FROM Cycles WHERE cycle_id = ? AND user_id = ?");
$stmt->execute([$cycle_id, $user_id]);
$cycle = $stmt->fetch();

if (!$cycle) {
    header("Location: index.php");
    exit;
}

// Process the form submission to update the cycle
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $start_date = $_POST["start_date"];
    $end_date = $_POST["end_date"];
    $flow_intensity = $_POST["flow_intensity"];

    // Update the cycle in the database
    $stmt = $pdo->prepare("UPDATE Cycles SET start_date = ?, end_date = ?, flow_intensity = ? WHERE cycle_id = ? AND user_id = ?");
    $stmt->execute([$start_date, $end_date, $flow_intensity, $cycle_id, $user_id]);

    // Redirect to the index page after successful update
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Cycle</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container">
    <h2>Edit Cycle</h2>

    <form method="post">
        <label>Start Date:</label>
        <input type="date" name="start_date" class="form-control" value="<?= $cycle['start_date'] ?>" required><br>

        <label>End Date:</label>
        <input type="date" name="end_date" class="form-control" value="<?= $cycle['end_date'] ?>" required><br>

        <label>Flow Intensity:</label>
        <select name="flow_intensity" class="form-control" required>
            <option value="Light" <?= $cycle['flow_intensity'] == 'Light' ? 'selected' : '' ?>>Light</option>
            <option value="Medium" <?= $cycle['flow_intensity'] == 'Medium' ? 'selected' : '' ?>>Medium</option>
            <option value="Heavy" <?= $cycle['flow_intensity'] == 'Heavy' ? 'selected' : '' ?>>Heavy</option>
        </select><br>

        <button type="submit" class="btn btn-success">Update</button>
    </form>
</body>
</html>
