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

	header('location: ../../admin/food-list.php');

	exit();
	
}
if ($_POST['name'] == "" || $_POST['desc'] == "" || $_POST['category'] == "" || $_POST['price'] == "") {

	$_SESSION['msg'] = 'All Fields are Compulsory!';

	header('location: ../../admin/food-list.php');

	exit();

}
if (!isset($_REQUEST['id'])) {

	$_SESSION['msg'] = 'Invalid ID';

	header('location: ../../admin/food-list.php');

	exit();

} else {

    $id= $_REQUEST['id'];
	$name = $_POST['name'];
    $d_status = $_POST['data_status'];
	$short_desc = $_POST['desc'];
	$price = $_POST['price'];
    $img = $_FILES['fimg']['name'];
	$temp = $_FILES['fimg']['tmp_name'];
	$folder = __DIR__ . "/../../images/" . $img;

    if($img == ""){
        $sql = "UPDATE food SET fname='$name', description='$short_desc', price='$price', data_status='$d_status' WHERE id='$id'";
    }else{
        $sql = "UPDATE food SET fname='$name', description='$short_desc', image='$img', price='$price', data_status='$d_status' WHERE id='$id'";
        move_uploaded_file($temp,$folder);
    }

    $query  = $pdoconn->prepare($sql);
    if ($query->execute()) {

    	$_SESSION['msg'] = 'Food Updated Succesfully!';

		header('location: ../../admin/food-list.php');
    	
    } else {

    	$_SESSION['msg'] = 'There were some problem in the server! Please try again after some time!';

		header('location: ../../admin/food-list.php');

    }


}