<?php

function veggieVillageEnsureDatabaseInitialized(string $host, string $user, string $pwd, string $database): void
{
    static $isChecked = false;

    if ($isChecked) {
        return;
    }
    $isChecked = true;

    $mysqli = @new mysqli($host, $user, $pwd);
    if ($mysqli->connect_errno) {
        throw new Exception('Database connection failed.');
    }

    $mysqli->set_charset('utf8mb4');

    $safeDatabase = '`' . str_replace('`', '``', $database) . '`';
    if (!$mysqli->query("CREATE DATABASE IF NOT EXISTS {$safeDatabase} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci")) {
        throw new Exception('Database setup failed.');
    }

    if (!$mysqli->select_db($database)) {
        throw new Exception('Database selection failed.');
    }

    $escapedDatabase = $mysqli->real_escape_string($database);
    $tableCountQuery = "SELECT COUNT(*) AS total FROM information_schema.tables WHERE table_schema = '{$escapedDatabase}'";
    $result = $mysqli->query($tableCountQuery);
    if (!$result) {
        throw new Exception('Database table check failed.');
    }

    $row = $result->fetch_assoc();
    $tableCount = (int) ($row['total'] ?? 0);
    $result->free();

    if ($tableCount !== 0) {
        $mysqli->close();
        return;
    }

    $sqlPath = realpath(__DIR__ . '/../veggei_village_db.sql');
    if ($sqlPath === false || !is_readable($sqlPath)) {
        throw new Exception('Database dump file not found.');
    }

    $sqlDump = file_get_contents($sqlPath);
    if ($sqlDump === false) {
        throw new Exception('Database dump read failed.');
    }

    if (!$mysqli->multi_query($sqlDump)) {
        throw new Exception('Database import failed.');
    }

    do {
        $queryResult = $mysqli->store_result();
        if ($queryResult instanceof mysqli_result) {
            $queryResult->free();
        }
    } while ($mysqli->more_results() && $mysqli->next_result());

    if ($mysqli->errno) {
        throw new Exception('Database import failed.');
    }

    $mysqli->close();
}

