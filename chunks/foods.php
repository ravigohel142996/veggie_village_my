
<?php

require __DIR__ . '/../backends/connection-pdo.php';

if (isset($_REQUEST['id'])) {

	$sql = 'SELECT * FROM food WHERE cat_id = "'.$_REQUEST['id'].'" AND data_status = "Active"';
	
} else {

	$sql = "SELECT * FROM food WHERE data_status = 'Active'";
 
}

$query  = $pdoconn->prepare($sql);
$query->execute();
$arr_all = $query->fetchAll(PDO::FETCH_ASSOC);

?>


<section class="fcategories">

    <div class="container">
    <?php

			if (isset($_SESSION['msg'])) {
				echo '<div class="section center" style="margin: 10px; padding: 3px 10px; margin-top: 35px; border: 2px solid black; border-radius: 5px; color: white;background-color:#3E7B27;">
						<p><b>'.$_SESSION['msg'].'</b></p>
					</div>';

				unset($_SESSION['msg']);
			}
		?>
        <div class="section white center">
            <h3 class="header" style="font-weight: 500;">Foods Area!</h3>
        </div>

        <?php if (count($arr_all) == 0) { ?>
            <div class="section gray center">
                <img class="activator" src="images/Designer (11).jpeg" style="width:100%;height:450px;border-radius: 10px;">
            </div>
        <?php } else { ?>

        <div class="row">
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">

                <?php foreach ($arr_all as $row) { ?>
                
                    <div class="card" style="display: flex; flex-direction: column; min-height: 450px; border-radius: 15px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2); overflow: hidden;">

                        <div class="card-image waves-effect waves-block waves-light" style="border-top-left-radius: 15px; border-top-right-radius: 15px;">
                            <img class="activator" src="images/<?php echo $row['image']; ?>" style="width:100%; height:200px; object-fit: cover;">
                        </div>

                        <div class="card-content" style="display: flex; flex-direction: column; flex-grow: 1; padding: 15px;">
                            <span class="card-title activator grey-text text-darken-4" style="font-weight: bold; font-size: 18px;"><?php echo $row['fname']; ?><i class="material-icons right">more_vert</i>
                            </span>
                            <p style="font-size: 15px; flex-grow: 1;"><?php echo $row['description']; ?></p>

                            <div style="text-align: center; padding-top: 15px;">
                                <p style="font-size: 15px; flex-grow: 1;font-weight: bold;padding-bottom:10px;">Price : ₹<?php echo $row['price']; ?></p>
                           
                                <a href="chunks/confrim-order.php?id=<?php echo $row['id']; ?>" class="waves-effect waves-light btn order-btn" style="background: linear-gradient(45deg, #3E7B27, #81B214); border-radius: 25px; color: #E6DD3B; font-weight: bold;">
									Order Now!
                                </a>
                            </div>
                        </div>
                        <div class="card-reveal">
                            <span class="card-title grey-text text-darken-4" style="font-weight: bold; font-size: 18px;"><?php echo $row['fname']; ?><i class="material-icons right">close</i></span>
                            <p style="font-size: 15px;"><?php echo $row['description']; ?></p>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>

        <?php } ?>
    </div>
</section>
