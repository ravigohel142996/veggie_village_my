<?php

require_once __DIR__ . '/bootstrap.php';
include_once __DIR__ . "/config.php";

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4";
    $pdoconn = new PDO($dsn, $user, $pwd, [
        PDO::ATTR_PERSISTENT => true,
        PDO::ATTR_TIMEOUT => 5,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    error_log('Database connection failed: ' . $e->getMessage());
    throw new Exception('Database connection failed.');
}

?>
