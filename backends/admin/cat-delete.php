<?php

session_start();
try {

    $connectionFile = __DIR__ . '/../connection-pdo.php';
    if (!file_exists($connectionFile))
        throw new Exception();
    else
        require_once($connectionFile); 
		
} catch (Exception $e) {

	$_SESSION['msg'] = 'There were some problem in the Server! Try after some time!';

	header('location: ../../admin/category-list.php');

	exit();
	
}

if (!isset($_REQUEST['id'])) {

	$_SESSION['msg'] = 'Invalid ID!';

	header('location: ../../admin/category-list.php');

	exit();
} 

	$id = $_REQUEST['id'];


	$sql = "DELETE FROM categories WHERE id = ?";
    $query  = $pdoconn->prepare($sql);
    if ($query->execute([$id])) {

    	$_SESSION['msg'] = 'Category Deleted!';

		header('location: ../../admin/category-list.php');
    	
    } else {

    	$_SESSION['msg'] = 'There were some problem in the server! Please try again after some time!';

		header('location: ../../admin/category-list.php');

    }

