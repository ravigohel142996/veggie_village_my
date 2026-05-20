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

	$_SESSION['msg'] = 'Invalid ID';

	header('location: ../../admin/offer-list.php');

	exit();

} else {

    $id= $_REQUEST['id'];
	$title = $_POST['title'];
    $d_status = $_POST['data_status'];
	$discount = $_POST['discount'];
	$desc = $_POST['desc'];
    $s_date = $_POST['s_date'];
    $e_date = $_POST['e_date'];
    $promo_code = $_POST['promo_code'];
    $status = $_POST['status'];
    $img = $_FILES['img']['name'];
	$temp = $_FILES['img']['tmp_name'];
	$folder = __DIR__ . "/../../images/" . $img;

    if($img == ""){
        $sql = "UPDATE offers SET title='$title', description='$desc', discount='$discount', promo_code='$promo_code', start_date='$s_date', end_date='$e_date', status='$status', data_status='$d_status' WHERE id='$id'";
    }else{
        $sql = "UPDATE offers SET title='$title', description='$desc', discount='$discount', promo_code='$promo_code', start_date='$s_date', end_date='$e_date', status='$status', image='$img', data_status='$d_status' WHERE id='$id'";
        move_uploaded_file($temp,$folder);
    }

    $query  = $pdoconn->prepare($sql);
    if ($query->execute()) {

    	$_SESSION['msg'] = 'Offer Updated Succesfully!';

		header('location: ../../admin/offer-list.php');
    	
    } else {

    	$_SESSION['msg'] = 'There were some problem in the server! Please try again after some time!';

		header('location: ../../admin/offer-list.php');

    }


}