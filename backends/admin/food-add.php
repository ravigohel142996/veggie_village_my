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

if (!isset($_POST['name']) || !isset($_POST['desc'])) {

	$_SESSION['msg'] = 'Invalid POST variable keys! Refresh the page!';

	header('location: ../../admin/food-list.php');

	exit();
}
if ($_POST['name'] == "" || $_POST['desc'] == "" || $_POST['category'] == "" || $_POST['price'] == "") {

	$_SESSION['msg'] = 'All Fields are Compulsory!';

	header('location: ../../admin/food-list.php');

	exit();

}

$regex = '/^[(A-Z)?(a-z)?(0-9)?\-?\_?\.?\s*]+$/';


if (!preg_match($regex, $_POST['name']) || !preg_match($regex, $_POST['desc'])) {

	$_SESSION['msg'] = 'Whoa! Invalid Inputs!';

	header('location: ../../admin/food-list.php');

	exit();

} else {

	$name = $_POST['name'];
	$d_status=$_POST['data_status'];
	$desc = $_POST['desc'];
	$category = $_POST['category'];
	$price = $_POST['price'];
	$img = $_FILES['fimg']['name'];
	$temp = $_FILES['fimg']['tmp_name'];
	$folder = __DIR__ . "/../../images/" . $img;
	move_uploaded_file($temp,$folder);

	$sql = "INSERT INTO food(cat_id,fname,description,image,price,data_status) VALUES(?,?,?,?,?,?)";
    $query  = $pdoconn->prepare($sql);
    if ($query->execute([$category, $name, $desc, $img, $price, $d_status])) {

    	$_SESSION['msg'] = 'Food Added!';

		header('location: ../../admin/food-list.php');
    	
    } else {

    	$_SESSION['msg'] = 'There were some problem in the server! Please try again after some time!';

		header('location: ../../admin/food-list.php');

    }


}