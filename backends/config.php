<?php

require_once __DIR__ . '/db-bootstrap.php';

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

try {
    veggieVillageEnsureDatabaseInitialized($host, $user, $pass, $db, $port);
} catch (Throwable $e) {
    error_log('Database bootstrap skipped or failed: ' . $e->getMessage());
}

?>
