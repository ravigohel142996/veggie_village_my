<?php
require __DIR__ . '/../backends/connection-pdo.php';

if (!isset($_REQUEST['id']) || empty($_REQUEST['id'])) {
    die("Invalid request.");
}

$q = "SELECT * FROM offers WHERE CURDATE() BETWEEN start_date AND end_date AND data_status = 'Active' AND NOT status = 'expired' ORDER BY end_date ASC";
$que = $pdoconn->prepare($q);
$que->execute();
$row = $que->rowCount();
$offers = $que->fetchAll();


$sql = 'SELECT * FROM food WHERE id = :id';
$query = $pdoconn->prepare($sql);
$foodId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if ($foodId === false || $foodId === null) {
    die("Food item not found.");
}
$query->bindParam(':id', $foodId);
$query->execute();
$arr_all = $query->fetch();

if (!$arr_all) {
    die("Food item not found.");
}
?>

<!DOCTYPE html>
<html lang="en" style="height:auto;min-height: 100vh;">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Order</title>
    <link rel="stylesheet" href="../css/materialize.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body id="c-order" style="height:auto;background: linear-gradient(to bottom right, #f0f4f8, #e9edf2);">
<form action="../backends/order-food.php?id=<?php echo $arr_all['id']; ?>" method="post" enctype="multipart/form-data" style="height:auto;margin-bottom:20px;">
    <div>
        <h2 class="main-heading">Order Confirmation</h2>

        <div class="order-container">
            <div class="image-container">
                <img src="../images/<?php echo $arr_all['image']; ?>" alt="Food Image">
            </div>
            
            <h4 class="order-title"><?php echo $arr_all['fname']; ?></h4>
            
            <p class="order-details"><?php echo $arr_all['description']; ?></p>

        <span>₹<span class="order-details" id="price" name="price"><?php echo $arr_all['price']; ?></span></span><br><br>
        <input type="text" name="price2" id="price2" value="<?php echo $arr_all['price']; ?>" style="display:none;">

        <div>
            <lable>Quantity :</lable><br>
            <div class="quantity-container">
                <button type="button" class="quantity-btn" id="quantity-sub" onclick="decreaseQuantity()">-</button>
                <input type="number" class="quantity-input" id="quantity" name="quantity" value="1" min="1" max="100" step="1">
                <button type="button" class="quantity-btn" id="quantity_add" onclick="increaseQuantity()">+</button>
            </div>
        </div>
        <div style="display:none;width:50%;background-color:lightgreen;margin:auto;border-radius:4px;" id="msg1">
        <lable>Offer Applied Successfully!</lable>
    </div>
        <?php 
if ($row > 0) { ?>
<div id="offerdiv">
<br><lable style="color: black;"><b>Select Offers :</b></lable>
    <div class="input-field" style="color: black !important; width: 90%;height:auto;margin:auto;margin-top:10px;">
		<select name='code' style="background-color:lightgray">
            <?php 

            foreach ($offers as $key) {
                echo '<option value="'.$key['promo_code'].'" data-discount="'.$key['discount'].'">'.$key['title'].'</option>';
            }
            ?>    
		</select>
	</div>
    <div>
        <input type="text" id="promo_code" name="promo_code" placeholder="Enter promo code" >
        <lable id="codelable" style="color:red;"></lable><br>
        <button type="button" id="apply" onclick="codefunction()">Apply</button>
    </div>
    
<?php } ?>
            </div>
            <input type="text" id="offer" name="offer" style="visibility:hidden">
            <div class="btn-container">
                <a href="../foods.php" class="order-btn" id="order-cancel-btn">Cancel</a>
                <button type="submit" class="order-btn" id="order-confrim-btn">Confirm Order</button>
            </div>
        </div>
    </div>
</form>
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="../js/ajax.js"></script>
</body>
</html>
