<?php

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$host = trim(getenv('DB_HOST'));
$user = trim(getenv('DB_USER'));
$pass = trim(getenv('DB_PASS'));
$db   = trim(getenv('DB_NAME'));
$port = intval(getenv('DB_PORT') ?: 3306);

if ($user === '' || $db === '') {
    error_log('Database connection failed: DB_USER and DB_NAME are required.');
    die('Database connection failed: invalid database credentials.');
}

if ($host === '' || strtolower($host) === 'localhost' || str_starts_with($host, '/')) {
    error_log('Database connection failed: DB_HOST must be a TCP host and cannot use localhost or a unix socket path.');
    die('Database connection failed: invalid DB_HOST.');
}

error_log('DB_HOST=' . $host);
error_log('DB_PORT=' . $port);

$conn = new mysqli($host, $user, $pass, $db, $port);

$conn->set_charset('utf8mb4');

if ($conn->connect_error) {
    error_log('MySQLi connection error: ' . $conn->connect_error);
    die('Database connection failed: ' . $conn->connect_error);
}

$conn->close();

?>
