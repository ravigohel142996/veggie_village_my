<?php

function veggieVillageEnsureDatabaseInitialized(string $host, string $user, string $password, string $database, int $port = 3306): void
{
    static $isChecked = false;

    if ($isChecked) {
        return;
    }
    $isChecked = true;

    if ($host === '' || strtolower($host) === 'localhost' || str_starts_with($host, '/')) {
        throw new Exception('Database connection failed: DB_HOST must be a TCP host and cannot use localhost or a unix socket path.');
    }

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $mysqli = new mysqli($host, $user, $password, '', $port);

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

    $expectedTables = veggieVillageExtractDumpTables($sqlDump);
    if (empty($expectedTables)) {
        throw new Exception('Database dump validation failed: no tables found.');
    }

    $missingTables = veggieVillageFindMissingTables($mysqli, $database, $expectedTables);
    if (empty($missingTables)) {
        $mysqli->close();
        return;
    }

    $filteredSqlDump = veggieVillageFilterDumpForMissingTables($sqlDump, $missingTables);

    // Import is restricted to trusted, local repository SQL dump files listed in $sqlPaths.
    if (!$mysqli->multi_query($filteredSqlDump)) {
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

function veggieVillageExtractDumpTables(string $sqlDump): array
{
    preg_match_all('/CREATE TABLE\s+`([^`]+)`/i', $sqlDump, $matches);
    if (!isset($matches[1])) {
        return [];
    }

    $tables = array_values(array_unique($matches[1]));
    sort($tables);

    return $tables;
}

function veggieVillageFindMissingTables(mysqli $mysqli, string $database, array $expectedTables): array
{
    if (empty($expectedTables)) {
        return [];
    }

    $placeholders = implode(',', array_fill(0, count($expectedTables), '?'));
    $types = str_repeat('s', count($expectedTables) + 1);
    $sql = "SELECT table_name FROM information_schema.tables WHERE table_schema = ? AND table_name IN ({$placeholders})";

    $stmt = $mysqli->prepare($sql);
    if (!$stmt) {
        throw new Exception('Database table check failed: ' . $mysqli->error);
    }

    $params = array_merge([$database], $expectedTables);
    $bindParams = [$types];
    foreach ($params as $key => $value) {
        $bindParams[] = &$params[$key];
    }

    if (!call_user_func_array([$stmt, 'bind_param'], $bindParams)) {
        $stmt->close();
        throw new Exception('Database table check failed: ' . $stmt->error);
    }

    if (!$stmt->execute()) {
        $stmt->close();
        throw new Exception('Database table check failed: ' . $stmt->error);
    }

    $result = $stmt->get_result();
    if (!$result) {
        $stmt->close();
        throw new Exception('Database table check failed: ' . $stmt->error);
    }

    $existingTables = [];
    while ($row = $result->fetch_assoc()) {
        $existingTables[] = (string) ($row['table_name'] ?? '');
    }

    $result->free();
    $stmt->close();

    return array_values(array_diff($expectedTables, $existingTables));
}

function veggieVillageFilterDumpForMissingTables(string $sqlDump, array $missingTables): string
{
    $missingSet = array_fill_keys($missingTables, true);
    $statements = veggieVillageSplitSqlStatements($sqlDump);
    $filteredStatements = [];

    foreach ($statements as $statement) {
        $trimmed = trim($statement);
        if ($trimmed === '') {
            continue;
        }

        if (preg_match('/^(CREATE TABLE|INSERT INTO|ALTER TABLE)\s+`([^`]+)`/i', $trimmed, $matches) === 1) {
            $tableName = $matches[2];
            if (!isset($missingSet[$tableName])) {
                continue;
            }
        }

        $filteredStatements[] = $trimmed;
    }

    return implode(";\n", $filteredStatements) . ";\n";
}

function veggieVillageSplitSqlStatements(string $sql): array
{
    $statements = [];
    $current = '';
    $inSingleQuote = false;
    $inDoubleQuote = false;
    $escaped = false;
    $length = strlen($sql);

    for ($i = 0; $i < $length; $i++) {
        $char = $sql[$i];

        if ($escaped) {
            $current .= $char;
            $escaped = false;
            continue;
        }

        if ($char === '\\' && ($inSingleQuote || $inDoubleQuote)) {
            $current .= $char;
            $escaped = true;
            continue;
        }

        if ($char === "'" && !$inDoubleQuote) {
            $inSingleQuote = !$inSingleQuote;
            $current .= $char;
            continue;
        }

        if ($char === '"' && !$inSingleQuote) {
            $inDoubleQuote = !$inDoubleQuote;
            $current .= $char;
            continue;
        }

        if ($char === ';' && !$inSingleQuote && !$inDoubleQuote) {
            $statements[] = $current;
            $current = '';
            continue;
        }

        $current .= $char;
    }

    if (trim($current) !== '') {
        $statements[] = $current;
    }

    return $statements;
}
