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

if (!isset($_POST['name']) || !isset($_POST['short_desc']) || !isset($_POST['long_desc'])) {

	$_SESSION['msg'] = 'Invalid POST variable keys! Refresh the page!';

	header('location: ../../admin/category-list.php');

	exit();
}

$regex = '/^[(A-Z)?(a-z)?(0-9)?\-?\_?\.?\,?\s*]+$/';


if ($_POST['name'] == "" || $_POST['short_desc'] == "" || $_POST['long_desc'] == "") {

	$_SESSION['msg'] = 'All Fields are Compulsory!';

	header('location: ../../admin/category-list.php');

	exit();

}

if (!preg_match($regex, $_POST['name']) || !preg_match($regex, $_POST['short_desc']) || !preg_match($regex, $_POST['long_desc'])) {

	$_SESSION['msg'] = 'Whoa! Invalid Inputs!';

	header('location: ../../admin/category-list.php');

	exit();

} else {

	$name = $_POST['name'];
	$data_status = $_POST['data_status'];
	$short_desc = $_POST['short_desc'];
	$long_desc = $_POST['long_desc'];
	$img = $_FILES['img']['name'];
	$temp = $_FILES['img']['tmp_name'];
	$folder = __DIR__ . "/../../images/" . $img;
	move_uploaded_file($temp,$folder);


	$sql = "INSERT INTO categories(name,short_desc,long_desc,image,data_status) VALUES(?,?,?,?,?)";
    $query  = $pdoconn->prepare($sql);
    if ($query->execute([$name, $short_desc, $long_desc, $img, $data_status])) {

    	$_SESSION['msg'] = 'Category Added!';

		header('location: ../../admin/category-list.php');
    	
    } else {

    	$_SESSION['msg'] = 'There were some problem in the server! Please try again after some time!';

		header('location: ../../admin/category-list.php');

    }


}