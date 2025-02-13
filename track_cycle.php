<?php
include 'db.php';
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $start_date = $_POST["start_date"];
    $end_date = date('Y-m-d', strtotime($start_date . ' +6 days')); // Auto-calculate end date
    $name = $_POST["name"]; // Assuming name input
    $flow_intensity = $_POST["flow_intensity"];
    $user_id = $_SESSION["user_id"];

    $stmt = $pdo->prepare("INSERT INTO Cycles (user_id, name, start_date, end_date, flow_intensity) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $name, $start_date, $end_date, $flow_intensity]);

    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Track Cycle</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script>
        function updateEndDate() {
            let startDate = document.getElementById("start_date").value;
            if (startDate) {
                let start = new Date(startDate);
                start.setDate(start.getDate() + 6);
                document.getElementById("end_date").value = start.toISOString().split("T")[0]; 
            }
        }
    </script>
</head>
<body class="container">
    <h2>Log Menstrual Cycle</h2>
    <form method="post">
        <label>Name:</label>
        <input type="text" name="name" class="form-control" placeholder="Enter Name" required><br>

        <label>Start Date:</label>
        <input type="date" id="start_date" name="start_date" class="form-control" required oninput="updateEndDate()"><br>

        <label>End Date:</label>
        <input type="date" id="end_date" name="end_date" class="form-control" readonly><br>

        <label>Flow Intensity:</label>
        <select name="flow_intensity" class="form-control">
            <option value="Light">Light</option>
            <option value="Medium">Medium</option>
            <option value="Heavy">Heavy</option>
        </select><br>

        <button type="submit" class="btn btn-success">Save</button>
        <a href="index.php" class="btn btn-secondary">Back</a>
    </form>
</body>
</html>
