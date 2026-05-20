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

if (!isset($_POST['title']) || !isset($_POST['discount']) || !isset($_POST['desc']) || !isset($_POST['s_date']) || !isset($_POST['e_date']) || !isset($_POST['promo_code']) || !isset($_POST['status'])) {

	$_SESSION['msg'] = 'Invalid POST variable keys! Refresh the page!';

	header('location: ../../admin/offer-list.php');

	exit();
}

$regex = '/^[(A-Z)?(a-z)?(0-9)?\-?\_?\.?\,?\s*]+$/';

if ($_POST['title'] == "" || $_POST['discount'] == "" || $_POST['desc'] == "" || $_POST['s_date'] == "" || $_POST['e_date'] == "" || $_POST['promo_code'] =="" || $_POST['status'] == "") {

	$_SESSION['msg'] = 'All Fields are Compulsory!';

	header('location: ../../admin/offer-list.php');

	exit();

} else {

	$title = $_POST['title'];
	$discount = $_POST['discount'];
	$desc = $_POST['desc'];
    $s_date = $_POST['s_date'];
    $e_date = $_POST['e_date'];
    $promo_code = $_POST['promo_code'];
    $status = $_POST['status'];
	$img = $_FILES['img']['name'];
	$temp = $_FILES['img']['tmp_name'];
	$folder = __DIR__ . "/../../images/" . $img;
	move_uploaded_file($temp,$folder);


	$sql = "INSERT INTO offers(title,description,discount,promo_code,start_date,end_date,status,image) VALUES(?,?,?,?,?,?,?,?)";
    $query  = $pdoconn->prepare($sql);
    if ($query->execute([$title, $desc, $discount, $promo_code, $s_date, $e_date, $status, $img])) {

    	$_SESSION['msg'] = 'Offer Added!';

		header('location: ../../admin/offer-list.php');
    	
    } else {

    	$_SESSION['msg'] = 'There were some problem in the server! Please try again after some time!';

		header('location: ../../admin/offer-list.php');

    }


}