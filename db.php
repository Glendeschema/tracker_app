<?php
// Database configuration
$serverName = "tcp:lesegostore.database.windows.net,1433";
$database = "AdventureWorksLT2022";
$username = "lesego";
$password = "Mpotu@2025";

try {
    // Establish PDO connection
    $pdo = new PDO("sqlsrv:Server=$serverName;Database=$database", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("Error connecting to SQL Server: " . $e->getMessage());
}
?>
