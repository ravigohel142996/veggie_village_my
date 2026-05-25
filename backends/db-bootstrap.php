<?php

const VEGGIE_VILLAGE_INITIAL_PAGE_VIEWS_ID = 1;
const VEGGIE_VILLAGE_INITIAL_VIEW_COUNT = 0;
const VEGGIE_VILLAGE_DB_CONNECT_TIMEOUT_SECONDS = 10;
const VEGGIE_VILLAGE_DB_CONNECT_RETRY_ATTEMPTS = 3;
const VEGGIE_VILLAGE_DB_CONNECT_RETRY_DELAY_MICROSECONDS = 250000;
const VEGGIE_VILLAGE_DB_BOOTSTRAP_FAILURE_COOLDOWN_SECONDS = 300;

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
    $insertIntoPattern = '/^\s*INSERT\s+INTO\s+/i';

    foreach ($statements as $statement) {
        if (preg_match($insertIntoPattern, $statement) === 1) {
            $normalizedStatements[] = preg_replace(
                $insertIntoPattern,
                'INSERT IGNORE INTO ',
                $statement,
                1
            ) ?? $statement;
            continue;
        }

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

function veggieVillageAllRequiredTablesExist(array $existingTables, array $requiredTables): bool
{
    foreach ($requiredTables as $requiredTable) {
        if (!isset($existingTables[strtolower($requiredTable)])) {
            return false;
        }
    }

    return true;
}

function veggieVillageTableHasRows(mysqli $mysqli, string $table): bool
{
    $allowedTables = array_map('strtolower', veggieVillageGetRequiredTables());
    if (!in_array(strtolower($table), $allowedTables, true)) {
        throw new Exception('Database table check failed: invalid table name.');
    }

    $safeTable = '`' . str_replace('`', '``', $table) . '`';
    $result = $mysqli->query("SELECT 1 FROM {$safeTable} LIMIT 1");

    if ($result === false) {
        throw new Exception('Database table check failed: ' . $mysqli->error);
    }

    $hasRows = $result->num_rows > 0;
    $result->free();

    return $hasRows;
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

function veggieVillageIsTransientConnectionError(string $message): bool
{
    $normalizedMessage = strtolower($message);
    $transientMarkers = veggieVillageGetTransientConnectionErrorMarkers();

    foreach ($transientMarkers as $marker) {
        if (str_contains($normalizedMessage, $marker)) {
            return true;
        }
    }

    return false;
}

function veggieVillageGetTransientConnectionErrorMarkers(): array
{
    return [
        'server has gone away',
        'error while reading greeting packet',
        'lost connection',
        'connection refused',
        'connection timed out',
        'timed out',
        'resource temporarily unavailable',
        'no route to host',
        'temporary failure',
        'try again',
    ];
}

function veggieVillageCreateMysqliConnectionWithRetry(
    string $host,
    string $user,
    string $password,
    string $database,
    int $port = 3306,
    int $maxRetries = VEGGIE_VILLAGE_DB_CONNECT_RETRY_ATTEMPTS
): mysqli {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $lastErrorMessage = 'Unknown MySQL connection error.';
    $maxRetries = max(1, $maxRetries);

    for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
        $conn = mysqli_init();
        if ($conn === false) {
            throw new Exception('Database connection failed: unable to initialize MySQL connection.');
        }

        mysqli_options($conn, MYSQLI_OPT_CONNECT_TIMEOUT, VEGGIE_VILLAGE_DB_CONNECT_TIMEOUT_SECONDS);

        try {
            $conn->real_connect($host, $user, $password, $database, $port);
            $conn->set_charset('utf8mb4');
            return $conn;
        } catch (mysqli_sql_exception $e) {
            $lastErrorMessage = $e->getMessage();
            if ($conn instanceof mysqli) {
                $conn->close();
            }

            $shouldRetry = $attempt < $maxRetries
                && veggieVillageIsTransientConnectionError($lastErrorMessage);

            if (!$shouldRetry) {
                break;
            }

            usleep(VEGGIE_VILLAGE_DB_CONNECT_RETRY_DELAY_MICROSECONDS * $attempt);
        }
    }

    throw new Exception('Database connection failed after retries: ' . $lastErrorMessage);
}

function veggieVillageGetBootstrapStatePath(string $host, string $database, int $port): string
{
    $stateKey = hash('sha256', strtolower(trim($host)) . '|' . strtolower(trim($database)) . '|' . (string) $port);
    return rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'veggie_village_db_bootstrap_' . $stateKey . '.json';
}

function veggieVillageReadBootstrapState(string $host, string $database, int $port): ?array
{
    $statePath = veggieVillageGetBootstrapStatePath($host, $database, $port);
    if (!is_readable($statePath)) {
        return null;
    }

    $rawState = file_get_contents($statePath);
    if ($rawState === false || trim($rawState) === '') {
        return null;
    }

    $state = json_decode($rawState, true);
    if (!is_array($state)) {
        return null;
    }

    $status = $state['status'] ?? null;
    $timestamp = $state['timestamp'] ?? null;
    if (!is_string($status) || !is_int($timestamp)) {
        return null;
    }

    return [
        'status' => $status,
        'timestamp' => $timestamp,
    ];
}

function veggieVillageWriteBootstrapState(string $host, string $database, int $port, string $status): void
{
    $statePath = veggieVillageGetBootstrapStatePath($host, $database, $port);
    $state = json_encode([
        'status' => $status,
        'timestamp' => time(),
    ]);

    if ($state === false) {
        return;
    }

    $writeResult = file_put_contents($statePath, $state, LOCK_EX);
    if ($writeResult === false) {
        error_log('Database bootstrap state write failed: ' . $statePath);
    }
}

function veggieVillageEnsureDatabaseInitialized(string $host, string $user, string $password, string $database, int $port = 3306): void
{
    static $isChecked = false;

    if ($isChecked) {
        return;
    }
    $isChecked = true;

    $bootstrapState = veggieVillageReadBootstrapState($host, $database, $port);
    if (is_array($bootstrapState)) {
        if (($bootstrapState['status'] ?? '') === 'success') {
            return;
        }

        if (
            ($bootstrapState['status'] ?? '') === 'failed'
            && (time() - (int) ($bootstrapState['timestamp'] ?? 0)) < VEGGIE_VILLAGE_DB_BOOTSTRAP_FAILURE_COOLDOWN_SECONDS
        ) {
            return;
        }
    }

    if ($host === '' || strtolower($host) === 'localhost' || str_starts_with($host, '/')) {
        throw new Exception('Database connection failed: DB_HOST must be a TCP host and cannot use localhost or a unix socket path.');
    }

    $mysqli = null;

    try {
        $mysqli = veggieVillageCreateMysqliConnectionWithRetry($host, $user, $password, '', $port);

        $safeDatabase = '`' . str_replace('`', '``', $database) . '`';
        if (!$mysqli->query("CREATE DATABASE IF NOT EXISTS {$safeDatabase} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci")) {
            throw new Exception('Database setup failed: ' . $mysqli->error);
        }

        if (!$mysqli->select_db($database)) {
            throw new Exception('Database selection failed: ' . $mysqli->error);
        }

        $requiredTables = veggieVillageGetRequiredTables();
        $existingTables = veggieVillageGetExistingTables($mysqli);
        $hasAllRequiredTables = veggieVillageAllRequiredTablesExist($existingTables, $requiredTables);
        $shouldRunImport = true;

        if ($hasAllRequiredTables) {
            $shouldRunImport = !veggieVillageTableHasRows($mysqli, 'admin');
        }

        if ($shouldRunImport) {
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

            veggieVillageExecuteStatements($mysqli, $statements);

            $existingTablesAfterImport = veggieVillageGetExistingTables($mysqli);
            foreach ($requiredTables as $requiredTable) {
                if (!isset($existingTablesAfterImport[strtolower($requiredTable)])) {
                    throw new Exception('Database import failed: required table "' . $requiredTable . '" is still missing.');
                }
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

        veggieVillageWriteBootstrapState($host, $database, $port, 'success');
    } catch (Throwable $e) {
        veggieVillageWriteBootstrapState($host, $database, $port, 'failed');
        throw $e;
    } finally {
        if ($mysqli instanceof mysqli) {
            $mysqli->close();
        }
    }
}
