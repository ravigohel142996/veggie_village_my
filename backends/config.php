<?php

require_once __DIR__ . '/bootstrap.php';

$conn = new mysqli(
    getenv('DB_HOST'),
    getenv('DB_USER'),
    getenv('DB_PASS'),
    getenv('DB_NAME'),
    intval(getenv('DB_PORT'))
);

if ($conn->connect_error) {
    $message = 'DB Connection Failed';
    if (vv_is_debug_enabled()) {
        $message .= ': ' . $conn->connect_error;
    }
    error_log('[VeggieVillage] ' . $message);
    die($message);
}

?>
