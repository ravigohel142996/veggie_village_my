<?php

$host = getenv("DB_HOST");
$user = getenv("DB_USER");
$pass = getenv("DB_PASS");
$db   = getenv("DB_NAME");
$port = getenv("DB_PORT") ?: 3306;

if (empty($host) || empty($user) || empty($db)) {
    error_log('Database connection failed: missing DB_HOST, DB_USER, or DB_NAME environment variable.');
    die('Database connection failed.');
}

$conn = new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    $appDebug = filter_var(getenv('APP_DEBUG') ?: 'false', FILTER_VALIDATE_BOOLEAN);
    die($appDebug ? ("Database connection failed: " . $conn->connect_error) : "Database connection failed.");
}

$pwd = $pass;
$database = $db;

?>
