<?php

function veggieVillageEnsureDatabaseInitialized(string $host, string $user, string $password, string $database): void
{
    static $isChecked = false;

    if ($isChecked) {
        return;
    }
    $isChecked = true;

    mysqli_report(MYSQLI_REPORT_OFF);
    $mysqli = new mysqli($host, $user, $password);

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

    $tableCountStmt = $mysqli->prepare('SELECT COUNT(*) AS total FROM information_schema.tables WHERE table_schema = ?');
    if (!$tableCountStmt) {
        throw new Exception('Database table check failed: ' . $mysqli->error);
    }
    $tableCountStmt->bind_param('s', $database);
    if (!$tableCountStmt->execute()) {
        throw new Exception('Database table check failed: ' . $tableCountStmt->error);
    }

    $result = $tableCountStmt->get_result();
    if (!$result) {
        throw new Exception('Database table check failed: ' . $tableCountStmt->error);
    }

    $row = $result->fetch_assoc();
    $tableCount = (int) ($row['total'] ?? 0);
    $result->free();
    $tableCountStmt->close();

    if ($tableCount !== 0) {
        $mysqli->close();
        return;
    }

    $sqlPaths = [
        __DIR__ . '/../veggie_village_db.sql',
        // Backward compatibility for the existing repository filename.
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
        throw new Exception('Failed to read database dump file: ' . $sqlPath);
    }

    // Import is restricted to trusted, local repository SQL dump files listed in $sqlPaths.
    if (!$mysqli->multi_query($sqlDump)) {
        throw new Exception('Database import failed: ' . $mysqli->error);
    }

    while (true) {
        $queryResult = $mysqli->store_result();
        if ($queryResult instanceof mysqli_result) {
            $queryResult->free();
        }

        if (!$mysqli->more_results()) {
            break;
        }

        if (!$mysqli->next_result()) {
            throw new Exception('Database import failed: ' . $mysqli->error);
        }
    }

    if ($mysqli->errno) {
        throw new Exception('Database import failed: ' . $mysqli->error);
    }

    $mysqli->close();
}
