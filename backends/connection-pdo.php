<?php

include_once __DIR__ . "/config.php";

if (!class_exists('VeggieVillageMysqliConnection')) {
    class VeggieVillageMysqliConnection
    {
        private mysqli $connection;

        public function __construct(mysqli $connection)
        {
            $this->connection = $connection;
            $this->connection->set_charset('utf8mb4');
        }

        public function prepare(string $query): VeggieVillageMysqliStatement
        {
            return new VeggieVillageMysqliStatement($this->connection, $query);
        }
    }
}

if (!class_exists('VeggieVillageMysqliStatement')) {
    class VeggieVillageMysqliStatement
    {
        private mysqli $connection;
        private mysqli_stmt $statement;
        private string $normalizedQuery;
        private array $namedPlaceholders = [];
        private array $boundParams = [];
        private ?array $rows = null;
        private int $rowCount = 0;
        private int $currentRowIndex = 0;

        public function __construct(mysqli $connection, string $query)
        {
            $this->connection = $connection;
            $this->normalizedQuery = preg_replace_callback(
                '/:([a-zA-Z_][a-zA-Z0-9_]*)/',
                function (array $matches): string {
                    $this->namedPlaceholders[] = $matches[1];
                    return '?';
                },
                $query
            ) ?? $query;

            $statement = $this->connection->prepare($this->normalizedQuery);
            if ($statement === false) {
                throw new Exception('Database query prepare failed.');
            }

            $this->statement = $statement;
        }

        public function bindParam(string|int $param, &$var, int $type = 0): bool
        {
            $this->boundParams[$this->normalizeParamKey($param)] = &$var;
            return true;
        }

        public function bindValue(string|int $param, mixed $value, int $type = 0): bool
        {
            $this->boundParams[$this->normalizeParamKey($param)] = $value;
            return true;
        }

        public function execute(array $params = []): bool
        {
            $resolvedParams = $params !== [] ? $params : $this->boundParams;
            $orderedParams = $this->orderParameters($resolvedParams);

            if ($orderedParams !== []) {
                $types = '';
                $bindValues = [];
                foreach ($orderedParams as $param) {
                    $types .= $this->resolveType($param);
                    $bindValues[] = $param;
                }
                $arguments = array_merge([$types], $this->toReferences($bindValues));
                $this->statement->bind_param(...$arguments);
            }

            $executed = $this->statement->execute();
            $this->rows = null;
            $this->rowCount = 0;

            if (!$executed) {
                return false;
            }

            $result = $this->statement->get_result();
            if ($result instanceof mysqli_result) {
                $this->rows = $result->fetch_all(MYSQLI_ASSOC);
                $this->rowCount = count($this->rows);
                $this->currentRowIndex = 0;
                $result->free();
            } else {
                $this->rowCount = max(0, (int) $this->statement->affected_rows);
                $this->currentRowIndex = 0;
            }

            return true;
        }

        public function fetch(int $mode = MYSQLI_ASSOC): array|false
        {
            $rows = $this->getRows();
            if ($rows === [] || !isset($rows[$this->currentRowIndex])) {
                return false;
            }

            $row = $rows[$this->currentRowIndex];
            $this->currentRowIndex++;
            return $row;
        }

        public function fetchAll(int $mode = MYSQLI_ASSOC): array
        {
            return $this->getRows();
        }

        public function rowCount(): int
        {
            return $this->rowCount;
        }

        private function getRows(): array
        {
            if (!is_array($this->rows)) {
                return [];
            }

            return $this->rows;
        }

        private function orderParameters(array $params): array
        {
            if ($this->namedPlaceholders === []) {
                if ($this->isSequentialArray($params)) {
                    return array_values($params);
                }

                $ordered = [];
                foreach ($params as $key => $value) {
                    $ordered[(int) $key] = $value;
                }
                ksort($ordered);
                return array_values($ordered);
            }

            $ordered = [];
            foreach ($this->namedPlaceholders as $placeholder) {
                if (array_key_exists($placeholder, $params)) {
                    $ordered[] = $params[$placeholder];
                    continue;
                }

                $prefixed = ':' . $placeholder;
                if (array_key_exists($prefixed, $params)) {
                    $ordered[] = $params[$prefixed];
                    continue;
                }

                throw new Exception('Database query execute failed: missing named parameter.');
            }

            return $ordered;
        }

        private function isSequentialArray(array $array): bool
        {
            return array_keys($array) === range(0, count($array) - 1);
        }

        private function normalizeParamKey(string|int $param): string|int
        {
            if (is_int($param)) {
                return $param - 1;
            }

            return ltrim($param, ':');
        }

        private function resolveType(mixed $value): string
        {
            if (is_int($value) || is_bool($value)) {
                return 'i';
            }

            if (is_float($value)) {
                return 'd';
            }

            return 's';
        }

        private function toReferences(array &$values): array
        {
            $references = [];
            foreach ($values as $key => &$value) {
                $references[$key] = &$value;
            }
            return $references;
        }
    }
}

$pdoconn = new VeggieVillageMysqliConnection($conn);

?>
