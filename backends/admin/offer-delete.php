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

	header('location: ../../admin/offer-list.php');

	exit();
	
}

if (!isset($_REQUEST['id'])) {

	$_SESSION['msg'] = 'Invalid ID!';

	header('location: ../../admin/offer-list.php');

	exit();
} 

	$id = $_REQUEST['id'];


	$sql = "DELETE FROM offers WHERE id = ?";
    $query  = $pdoconn->prepare($sql);
    if ($query->execute([$id])) {

    	$_SESSION['msg'] = 'Offer Deleted!';

		header('location: ../../admin/offer-list.php');
    	
    } else {

    	$_SESSION['msg'] = 'There were some problem in the server! Please try again after some time!';

		header('location: ../../admin/offer-list.php');

    }

