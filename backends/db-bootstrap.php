<?php

const VEGGIE_VILLAGE_INITIAL_PAGE_VIEWS_ID = 1;
const VEGGIE_VILLAGE_INITIAL_VIEW_COUNT = 0;

function veggieVillageLoadSqlStatements(string $sqlDump): array
{
    $statements = [];
    $buffer = '';
    $length = strlen($sqlDump);
    $inSingleQuote = false;
    $inDoubleQuote = false;
    $escaped = false;

    for ($position = 0; $position < $length; $position++) {
        $char = $sqlDump[$position];
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

function veggieVillageNormalizeSqlStatements(array $statements): array
{
    $normalizedStatements = [];

    foreach ($statements as $statement) {
        if (preg_match('/^\s*CREATE\s+TABLE\s+IF\s+NOT\s+EXISTS\s+/i', $statement) === 1) {
            $normalizedStatements[] = $statement;
            continue;
        }

        $normalizedStatements[] = preg_replace(
            '/^\s*CREATE\s+TABLE\s+/i',
            'CREATE TABLE IF NOT EXISTS ',
            $statement,
            1
        ) ?? $statement;
    }

    return $normalizedStatements;
}

function veggieVillageGetExistingTables(mysqli $mysqli): array
{
    $existingTables = [];
    $result = $mysqli->query('SHOW TABLES');
    if (!$result) {
        throw new Exception('Database table check failed: ' . $mysqli->error);
    }

    while ($row = $result->fetch_row()) {
        $tableName = $row[0] ?? '';
        if ($tableName !== '') {
            $existingTables[strtolower($tableName)] = true;
        }
    }

    $result->free();

    return $existingTables;
}

function veggieVillageShouldIgnoreSqlError(int $errorCode, string $statement): bool
{
    $normalizedStatement = strtoupper(ltrim($statement));

    if ($errorCode === 1050 && str_starts_with($normalizedStatement, 'CREATE TABLE')) {
        return true;
    }

    if ($errorCode === 1062 && str_starts_with($normalizedStatement, 'INSERT')) {
        return true;
    }

    if (
        ($errorCode === 1060 || $errorCode === 1061 || $errorCode === 1068)
        && str_starts_with($normalizedStatement, 'ALTER TABLE')
    ) {
        return true;
    }

    return false;
}

function veggieVillageExecuteStatements(mysqli $mysqli, array $statements): void
{
    foreach ($statements as $statement) {
        try {
            $queryResult = $mysqli->query($statement);
            if ($queryResult === false) {
                $errorCode = (int) $mysqli->errno;
                if (veggieVillageShouldIgnoreSqlError($errorCode, $statement)) {
                    continue;
                }

                throw new Exception('Database import failed: ' . $mysqli->error);
            }

            if ($queryResult instanceof mysqli_result) {
                $queryResult->free();
            }
        } catch (mysqli_sql_exception $e) {
            if (veggieVillageShouldIgnoreSqlError((int) $e->getCode(), $statement)) {
                continue;
            }

            throw new Exception('Database import failed: ' . $e->getMessage());
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

    $statements = veggieVillageNormalizeSqlStatements(veggieVillageLoadSqlStatements($sqlDump));
    if ($statements === []) {
        throw new Exception('Database dump file is empty: ' . $sqlPath);
    }

    $requiredTables = veggieVillageGetRequiredTables();
    veggieVillageExecuteStatements($mysqli, $statements);

    $existingTablesAfterImport = veggieVillageGetExistingTables($mysqli);
    foreach ($requiredTables as $requiredTable) {
        if (!isset($existingTablesAfterImport[strtolower($requiredTable)])) {
            throw new Exception('Database import failed: required table "' . $requiredTable . '" is still missing.');
        }
    }

    $pageViewsSeedStmt = $mysqli->prepare('INSERT IGNORE INTO page_views (id, view_count) VALUES (?, ?)');
    if (!$pageViewsSeedStmt) {
        throw new Exception('Database setup failed: unable to prepare page_views seed statement. ' . $mysqli->error);
    }

    $pageViewsId = VEGGIE_VILLAGE_INITIAL_PAGE_VIEWS_ID;
    $initialViewCount = VEGGIE_VILLAGE_INITIAL_VIEW_COUNT;
    $pageViewsSeedStmt->bind_param('ii', $pageViewsId, $initialViewCount);
    if (!$pageViewsSeedStmt->execute()) {
        throw new Exception('Database setup failed: unable to seed page_views table. ' . $pageViewsSeedStmt->error);
    }
    $pageViewsSeedStmt->close();

    $mysqli->close();
}
