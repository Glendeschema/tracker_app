<?php
include 'db.php';
session_start();

// Ensure user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];
$cycle_id = $_GET['id'] ?? null;

// Fetch cycle details
if ($cycle_id) {
    $stmt = $pdo->prepare("SELECT * FROM Cycles WHERE id = ? AND user_id = ?");
    $stmt->execute([$cycle_id, $user_id]);
    $cycle = $stmt->fetch();

    if (!$cycle) {
        die("Cycle not found.");
    }
}

// Handle update request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $start_date = $_POST["start_date"];
    $end_date = $_POST["end_date"];

    $stmt = $pdo->prepare("UPDATE Cycles SET name = ?, start_date = ?, end_date = ? WHERE id = ? AND user_id = ?");
    $stmt->execute([$name, $start_date, $end_date, $cycle_id, $user_id]);

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
        <label>Name:</label>
        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($cycle['name']) ?>" required><br>
        <label>Start Date:</label>
        <input type="date" name="start_date" class="form-control" value="<?= $cycle['start_date'] ?>" required><br>
        <label>End Date:</label>
        <input type="date" name="end_date" class="form-control" value="<?= $cycle['end_date'] ?>" required><br>
        <button type="submit" class="btn btn-success">Update</button>
        <a href="index.php" class="btn btn-secondary">Cancel</a>
    </form>
</body>
</html>
