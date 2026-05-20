<?php

function veggieVillageEnsureDatabaseInitialized(string $host, string $user, string $pwd, string $database): void
{
    static $isChecked = false;

    if ($isChecked) {
        return;
    }
    $isChecked = true;

    mysqli_report(MYSQLI_REPORT_OFF);
    $mysqli = new mysqli($host, $user, $pwd);

    if ($mysqli->connect_errno) {
        throw new Exception('Database connection failed: ' . $mysqli->connect_error);
    }

    $mysqli->set_charset('utf8mb4');

    $safeDatabase = '`' . str_replace('`', '``', $database) . '`';
    if (!$mysqli->query("CREATE DATABASE IF NOT EXISTS {$safeDatabase} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci")) {
        throw new Exception('Database setup failed: ' . $mysqli->error);
    }

    if (!$mysqli->select_db($database)) {
        throw new Exception('Database selection failed: ' . $mysqli->error);
    }

    $escapedDatabase = $mysqli->real_escape_string($database);
    $tableCountQuery = "SELECT COUNT(*) AS total FROM information_schema.tables WHERE table_schema = '{$escapedDatabase}'";
    $result = $mysqli->query($tableCountQuery);
    if (!$result) {
        throw new Exception('Database table check failed: ' . $mysqli->error);
    }

    $row = $result->fetch_assoc();
    $tableCount = (int) ($row['total'] ?? 0);
    $result->free();

    if ($tableCount !== 0) {
        $mysqli->close();
        return;
    }

    $sqlPaths = [
        __DIR__ . '/../veggie_village_db.sql',
        __DIR__ . '/../veggei_village_db.sql',
    ];

    $sqlPath = null;
    foreach ($sqlPaths as $candidatePath) {
        $resolvedPath = realpath($candidatePath);
        if ($resolvedPath !== false && is_readable($resolvedPath)) {
            $sqlPath = $resolvedPath;
            break;
        }
    }

    if ($sqlPath === null) {
        throw new Exception('Database dump file not found.');
    }

    $sqlDump = file_get_contents($sqlPath);
    if ($sqlDump === false) {
        throw new Exception('Database dump read failed: ' . $sqlPath);
    }

    if (!$mysqli->multi_query($sqlDump)) {
        throw new Exception('Database import failed: ' . $mysqli->error);
    }

    do {
        $queryResult = $mysqli->store_result();
        if ($queryResult instanceof mysqli_result) {
            $queryResult->free();
        }
    } while ($mysqli->more_results() && $mysqli->next_result());

    if ($mysqli->errno) {
        throw new Exception('Database import failed: ' . $mysqli->error);
    }

    $mysqli->close();
}
