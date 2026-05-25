<?php
session_start();

try {
    $connectionFile = __DIR__ . '/connection-pdo.php';
    if (!file_exists($connectionFile)) throw new Exception();
    else require_once($connectionFile);
} catch (Exception $e) {
    echo json_encode(['code'=>"0", 'msg'=>"There were some problem in the Server! Try after some time!"]);
    exit();
}

if (!isset($_SESSION['user']) || !isset($_SESSION['user_id'])) {
    $_SESSION['msg'] = "You must Log In First to Order Food!";
    header('Location: ../foods.php');
    exit();
}

if (!isset($_REQUEST['id'])) {
    $_SESSION['msg'] = "Invalid food item! Please try again!";
    header('Location: ../foods.php');
    exit();
}

date_default_timezone_set("Asia/Kolkata");

$food_id = $_REQUEST['id'];
$user_name = $_SESSION['user'];
$user_id = $_SESSION['user_id'];
$price = $_POST['price2'];
$quntity = $_POST['quantity'];
$code = $_POST['offer'];

if ($code == "") {
    $offertitle = "No";
} else {
    $query2 = $pdoconn->prepare("SELECT * FROM offers WHERE promo_code = ?");
    $query2->execute([$code]);
    $offercode = $query2->fetch();
    $offertitle = $offercode['title'] ?? "Invalid";
}

$order_id = "VG" . mt_rand(100000, 999999);
$timest = date("d:m:Y h:i:sa");

$sql = "INSERT INTO orders(order_id, user_id, food_id, user_name, timestamp, price, quantity, offer)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

$query = $pdoconn->prepare($sql);

if ($query->execute([$order_id, $user_id, $food_id, $user_name, $timest, $price, $quntity, $offertitle])) {
    header('Location: download-order-page.php?' . http_build_query([
        'order_id' => $order_id,
        'user_id' => $user_id,
        'food_id' => $food_id,
        'user_name' => $user_name,
        'timestamp' => $timest,
        'price' => $price,
        'quantity' => $quntity,
        'offer' => $offertitle
    ]));
    exit();
} else {
    $_SESSION['msg'] = "There was a problem in the server. Try again.";
    header('Location: ../foods.php');
    exit();
}
?>