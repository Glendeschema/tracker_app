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
    $name = $_POST["name"];  // Add name input field
    $user_id = $_SESSION["user_id"];

    // Insert the new cycle with the name field
    $stmt = $pdo->prepare("INSERT INTO Cycles (user_id, start_date, end_date, name) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user_id, $start_date, $end_date, $name]);

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
        <label>Cycle Name:</label>  <!-- New field for entering the cycle's name -->
        <input type="text" name="name" class="form-control" placeholder="Enter a name for this cycle" required><br>
        <button type="submit" class="btn btn-success">Save</button>
    </form>
    <a href="index.php" class="btn btn-secondary mt-3">Back to Dashboard</a>

</body>
</html>
