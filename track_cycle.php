<?php
include 'db.php';
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $start_date = $_POST["start_date"];
    $end_date = $_POST["end_date"];
    $flow_intensity = $_POST["flow_intensity"];
    $user_id = $_SESSION["user_id"];

    $stmt = $pdo->prepare("INSERT INTO Cycles (user_id, start_date, end_date, flow_intensity) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user_id, $start_date, $end_date, $flow_intensity]);

    header("Location: index.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Track Cycle</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container">
    <h2>Log Menstrual Cycle</h2>
    <form method="post">
        <label>Start Date:</label>
        <input type="date" name="start_date" class="form-control" required><br>
        <label>End Date:</label>
        <input type="date" name="end_date" class="form-control" required><br>
        <label>Flow Intensity:</label>
        <select name="flow_intensity" class="form-control">
            <option value="Light">Light</option>
            <option value="Medium">Medium</option>
            <option value="Heavy">Heavy</option>
        </select><br>
        <button type="submit" class="btn btn-success">Save</button>
    </form>
</body>
</html>