<?php
include 'db.php';
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];
$cycle_id = isset($_GET['id']) ? $_GET['id'] : null;

if ($cycle_id) {
    // Fetch the cycle to be edited
    $stmt = $pdo->prepare("SELECT * FROM Cycles WHERE user_id = ? AND cycle_id = ?");
    $stmt->execute([$user_id, $cycle_id]);
    $cycle = $stmt->fetch(PDO::FETCH_ASSOC);

    // If cycle doesn't exist, redirect
    if (!$cycle) {
        header("Location: index.php");
        exit;
    }

    // Calculate the default end date (+7 days from start date)
    $start_date = new DateTime($cycle["start_date"]);
    $start_date->modify('+7 days');
    $end_date = $start_date->format('Y-m-d');

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $new_start_date = $_POST["start_date"];
        $new_end_date = $_POST["end_date"];
        $flow_intensity = $_POST["flow_intensity"];
        
        // Update the cycle in the database
        $stmt = $pdo->prepare("UPDATE Cycles SET start_date = ?, end_date = ?, flow_intensity = ? WHERE cycle_id = ?");
        $stmt->execute([$new_start_date, $new_end_date, $flow_intensity, $cycle_id]);
        
        // Redirect to the dashboard after update
        header("Location: index.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Cycle</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container">
    <h2>Edit Menstrual Cycle</h2>
    <form method="post">
        <label>Start Date:</label>
        <input type="date" name="start_date" class="form-control" value="<?= $cycle["start_date"] ?>" required><br>
        <label>End Date:</label>
        <input type="date" name="end_date" class="form-control" value="<?= $end_date ?>" required><br>
        <label>Flow Intensity:</label>
        <select name="flow_intensity" class="form-control">
            <option value="Light" <?= $cycle["flow_intensity"] == "Light" ? 'selected' : '' ?>>Light</option>
            <option value="Medium" <?= $cycle["flow_intensity"] == "Medium" ? 'selected' : '' ?>>Medium</option>
            <option value="Heavy" <?= $cycle["flow_intensity"] == "Heavy" ? 'selected' : '' ?>>Heavy</option>
        </select><br>
        <button type="submit" class="btn btn-success">Save</button>
    </form>
</body>
</html>
