<?php

require_once __DIR__ . '/bootstrap.php';
include_once __DIR__ . "/config.php";

if (!function_exists('veggieVillageIsTransientPdoConnectionError')) {
    function veggieVillageIsTransientPdoConnectionError(string $message): bool
    {
        $normalizedMessage = strtolower($message);
        $transientMarkers = function_exists('veggieVillageGetTransientConnectionErrorMarkers')
            ? veggieVillageGetTransientConnectionErrorMarkers()
            : ['server has gone away', 'error while reading greeting packet', 'lost connection', 'connection refused', 'connection timed out', 'timed out', 'resource temporarily unavailable', 'no route to host', 'temporary failure', 'try again'];

        foreach ($transientMarkers as $marker) {
            if (str_contains($normalizedMessage, $marker)) {
                return true;
            }
        }

        return false;
    }
}

if (!function_exists('veggieVillageCreatePdoConnectionWithRetry')) {
    function veggieVillageCreatePdoConnectionWithRetry(string $dsn, string $user, string $pass, int $maxRetries = 3): PDO
    {
        $maxRetries = max(1, $maxRetries);
        $lastErrorMessage = 'Unknown PDO connection error.';

        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            try {
                return new PDO($dsn, $user, $pass, [
                    PDO::ATTR_PERSISTENT => false,
                    PDO::ATTR_TIMEOUT => 10,
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]);
            } catch (PDOException $e) {
                $lastErrorMessage = $e->getMessage();
                $shouldRetry = $attempt < $maxRetries
                    && veggieVillageIsTransientPdoConnectionError($lastErrorMessage);

                if (!$shouldRetry) {
                    break;
                }

                $retryDelay = defined('VEGGIE_VILLAGE_DB_CONNECT_RETRY_DELAY_MICROSECONDS')
                    ? VEGGIE_VILLAGE_DB_CONNECT_RETRY_DELAY_MICROSECONDS
                    : 250000;
                usleep($retryDelay * $attempt);
            }
        }

        throw new Exception('Database connection failed after retries: ' . $lastErrorMessage);
    }
}

if (!function_exists('veggieVillageGetSharedPdoConnection')) {
    function veggieVillageGetSharedPdoConnection(string $dsn, string $user, string $pass): PDO
    {
        static $sharedPdo = null;
        static $lastHealthCheckAt = 0;

        if ($sharedPdo instanceof PDO) {
            $currentTime = time();
            $shouldHealthCheck = ($currentTime - $lastHealthCheckAt) >= 30;
            if ($shouldHealthCheck) {
                try {
                    $sharedPdo->query('SELECT 1');
                    $lastHealthCheckAt = $currentTime;
                    return $sharedPdo;
                } catch (Throwable $connectionLost) {
                    $sharedPdo = null;
                }
            } else {
                return $sharedPdo;
            }
        }

        $sharedPdo = veggieVillageCreatePdoConnectionWithRetry($dsn, $user, $pass);
        $lastHealthCheckAt = time();
        return $sharedPdo;
    }
}

try {
    $dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";
    $pdoconn = veggieVillageGetSharedPdoConnection($dsn, $user, $pass);
} catch (Throwable $e) {
    throw new Exception('Database connection failed: ' . $e->getMessage());
}

?>
