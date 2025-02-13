<?php
$host = "lesegostore.database.windows.net";
$dbname = "AdventureWorksLT2022";
$username = "lesego";
$password = "Mpotu@2025";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
