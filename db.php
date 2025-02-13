<?php
// PHP Data Objects(PDO) Sample Code:
try {
    $conn = new PDO("sqlsrv:server = tcp:lesegostore.database.windows.net,1433; Database = AdventureWorksLT2022", "lesego", "Mpotu@2025");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch (PDOException $e) {
    print("Error connecting to SQL Server.");
    die(print_r($e));
}

// SQL Server Extension Sample Code:
$connectionInfo = array("UID" => "lesego", "pwd" => "Mpotu@2025", "Database" => "AdventureWorksLT2022", "LoginTimeout" => 30, "Encrypt" => 1, "TrustServerCertificate" => 0);
$serverName = "tcp:lesegostore.database.windows.net,1433";
$conn = sqlsrv_connect($serverName, $connectionInfo);
?>
