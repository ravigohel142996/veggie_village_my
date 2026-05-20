<?php
require __DIR__ . '/../backends/connection-pdo.php';

	if (!isset($_SESSION['visited'])) {
        $_SESSION['visited'] = true;
    
        // Increment the view count
        $sql = $pdoconn->prepare("UPDATE page_views SET view_count = view_count + 1 WHERE id = 1");
        $sql->execute();
    }
    // Get the updated view count
    $result = $pdoconn->prepare("SELECT view_count FROM page_views WHERE id = 1");
    $result->execute();
?>