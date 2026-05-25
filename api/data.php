<?php

try {

    $connectionFile = __DIR__ . '/../backends/connection-pdo.php';
    if (!file_exists($connectionFile))
        throw new Exception();
    else
        require_once($connectionFile); 
		
} catch (Exception $e) {

	$arr = array ('code'=>"0",'msg'=>"There were some problem in the Server! Try after some time!");

	echo json_encode($arr);

	exit();
	
}

if (!isset($_REQUEST['key'])) {
	$arr = array ('msg'=>"User Data API", 'dev'=>"Prem Agraavat");

	echo json_encode($arr);

	exit();

} else {

	if (strcmp('prem', $_REQUEST['key']) == 0) {

		$sql = "SELECT * FROM users;";
        $query  = $pdoconn->prepare($sql);
        $query->execute();
        $arr = $query->fetchAll();

		echo json_encode($arr);
	} else {

		$arr = array ('code'=>"0",'msg'=>"Invalid API Key!");

		echo json_encode($arr);
	}


	exit();
}