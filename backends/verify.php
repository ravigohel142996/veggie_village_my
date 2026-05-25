<?php 
try {

    $connectionFile = __DIR__ . '/connection-pdo.php';
    if (!file_exists($connectionFile))
        throw new Exception();
    else
        require_once($connectionFile); 
		
} catch (Exception $e) {

	$arr = array ('code'=>"0",'msg'=>"There were some problem in the Server! Try after some time!");

	echo json_encode($arr);

	exit();
	
}
if(isset($_GET['email']) && isset($_GET['v_code'])){
    $query  = $pdoconn->prepare("SELECT * FROM users WHERE email = '$_GET[email]' AND verification_code = '$_GET[v_code]'");
	if ($query->execute()){
        $row  = $query->rowCount();
        if($row == 1){
            $result_fetch = $query->fetch();
            if($result_fetch['is_verified'] == 0){
                $update  = $pdoconn->prepare("UPDATE users SET is_verified = '1' WHERE email = '$result_fetch[email]'");
                if($update->execute()){
                    echo "<script>alert('Email Verification Successful');window.location.href='/';</script>";

                }
            }else{
                echo "<script>alert('Email already registerd')</script>";
            }
        }
    }else{
        echo "<script>alert('Cannot run query')</script>";
    }
}


?>