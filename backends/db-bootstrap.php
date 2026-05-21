<?php

function veggieVillageLoadSqlStatements(string $sqlDump): array
{
    $statements = [];
    $buffer = '';
    $length = strlen($sqlDump);
    $inSingleQuote = false;
    $inDoubleQuote = false;
    $escaped = false;

    for ($index = 0; $index < $length; $index++) {
        $char = $sqlDump[$index];
        $buffer .= $char;

        if ($escaped) {
            $escaped = false;
            continue;
        }

        if ($char === '\\') {
            $escaped = true;
            continue;
        }

        if (!$inDoubleQuote && $char === "'") {
            $inSingleQuote = !$inSingleQuote;
            continue;
        }

        if (!$inSingleQuote && $char === '"') {
            $inDoubleQuote = !$inDoubleQuote;
            continue;
        }

        if (!$inSingleQuote && !$inDoubleQuote && $char === ';') {
            $statement = trim($buffer);
            if ($statement !== '') {
                $statements[] = $statement;
            }
            $buffer = '';
        }
    }

    $remaining = trim($buffer);
    if ($remaining !== '') {
        $statements[] = $remaining;
    }

    return $statements;
}

function veggieVillageGetRequiredTables(): array
{
    return ['admin', 'categories', 'food', 'offers', 'orders', 'page_views', 'users'];
}

function veggieVillageGetExistingTables(mysqli $mysqli, string $database): array
{
    $existingTables = [];
    $tableNamesStmt = $mysqli->prepare('SELECT table_name FROM information_schema.tables WHERE table_schema = ?');
    if (!$tableNamesStmt) {
        throw new Exception('Database table check failed: ' . $mysqli->error);
    }

    $tableNamesStmt->bind_param('s', $database);
    if (!$tableNamesStmt->execute()) {
        throw new Exception('Database table check failed: ' . $tableNamesStmt->error);
    }

    $result = $tableNamesStmt->get_result();
    if (!$result) {
        throw new Exception('Database table check failed: ' . $tableNamesStmt->error);
    }

    while ($row = $result->fetch_assoc()) {
        $tableName = $row['table_name'] ?? '';
        if ($tableName !== '') {
            $existingTables[$tableName] = true;
        }
    }

    $result->free();
    $tableNamesStmt->close();

    return $existingTables;
}

function veggieVillageFilterStatementsForTables(array $statements, array $tables): array
{
    if ($tables === []) {
        return [];
    }

    $filteredStatements = [];
    foreach ($statements as $statement) {
        $trimmed = ltrim($statement);
        if ($trimmed === '') {
            continue;
        }

        if (
            str_starts_with($trimmed, 'SET ')
            || str_starts_with($trimmed, 'START TRANSACTION')
            || str_starts_with($trimmed, 'COMMIT')
            || str_starts_with($trimmed, '/*!')
        ) {
            $filteredStatements[] = $statement;
            continue;
        }

        foreach ($tables as $table) {
            $pattern = '/(?:`' . preg_quote($table, '/') . '`|\b' . preg_quote($table, '/') . '\b)/i';
            if (preg_match($pattern, $statement) === 1) {
                $filteredStatements[] = $statement;
                break;
            }
        }
    }

    return $filteredStatements;
}

function veggieVillageExecuteStatements(mysqli $mysqli, array $statements): void
{
    foreach ($statements as $statement) {
        if (!$mysqli->query($statement)) {
            throw new Exception('Database import failed: ' . $mysqli->error);
        }
    }
}

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

    $statements = veggieVillageLoadSqlStatements($sqlDump);
    if ($statements === []) {
        throw new Exception('Database dump file is empty: ' . $sqlPath);
    }

    $requiredTables = veggieVillageGetRequiredTables();
    $existingTables = veggieVillageGetExistingTables($mysqli, $database);

    $missingTables = [];
    foreach ($requiredTables as $requiredTable) {
        if (!isset($existingTables[$requiredTable])) {
            $missingTables[] = $requiredTable;
        }
    }

    if ($missingTables !== []) {
        $isFirstImport = count($existingTables) === 0;

        if ($isFirstImport) {
            veggieVillageExecuteStatements($mysqli, $statements);
        } else {
            $filteredStatements = veggieVillageFilterStatementsForTables($statements, $missingTables);
            if ($filteredStatements === []) {
                throw new Exception('Database import failed: dump does not contain required missing table definitions.');
            }
            veggieVillageExecuteStatements($mysqli, $filteredStatements);
        }
    }

    $existingTablesAfterImport = veggieVillageGetExistingTables($mysqli, $database);
    foreach ($requiredTables as $requiredTable) {
        if (!isset($existingTablesAfterImport[$requiredTable])) {
            throw new Exception('Database import failed: required table "' . $requiredTable . '" is still missing.');
        }
    }

    $pageViewSeedSql = 'INSERT IGNORE INTO page_views (id, view_count) VALUES (1, 0)';
    if (!$mysqli->query($pageViewSeedSql)) {
        throw new Exception('Database setup failed: unable to seed page_views table. ' . $mysqli->error);
    }

    $mysqli->close();
}
