<?php
require __DIR__ . '/../backends/connection-pdo.php';

if (!isset($_SESSION['visited'])) {
    try {
        $_SESSION['visited'] = true;
        $sql = $pdoconn->prepare("UPDATE page_views SET view_count = view_count + 1 WHERE id = 1");
        $sql->execute();
    } catch (Throwable $e) {
        vv_log_exception($e);
        unset($_SESSION['visited']);
    }
}

try {
    $result = $pdoconn->prepare("SELECT view_count FROM page_views WHERE id = 1");
    $result->execute();
} catch (Throwable $e) {
    vv_log_exception($e);
}
?>