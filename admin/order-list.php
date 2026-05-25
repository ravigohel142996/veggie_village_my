<?php require('layout/header.php'); ?>
<?php require('layout/topnav.php'); ?>
<?php require('layout/left-sidebar-long.php'); ?>
<?php require('layout/left-sidebar-short.php'); ?>


<?php

require __DIR__ . '/../backends/connection-pdo.php';

$sql = 'SELECT orders.offer,orders.quantity, orders.price, orders.order_id, orders.user_name, orders.timestamp, food.fname FROM orders LEFT JOIN food ON orders.food_id = food.id';

$query  = $pdoconn->prepare($sql);
$query->execute();
$arr_all = $query->fetchAll();



?>
						

<div class="content">

	<div>
		<h3 style="padding-top:35px;">Orders</h3>
	</div>

  <?php

    if (isset($_SESSION['msg'])) {
        echo '<div class="section center" style="margin: 5px 35px;"><div class="row" style="background: #155724; color: white;">
        <div class="col s12">
            <h6>'.$_SESSION['msg'].'</h6>
            </div>
        </div></div>';
        unset($_SESSION['msg']);
    }

    ?>
	
	<div class="section center" style="padding: 20px;">
		<table class="centered responsive-table">
        <thead>
          <tr>
              <th>Order ID</th>
              <th>User Name</th>
              <th>Food Name</th>
              <th>Quantity</th>
              <th>Total Price</th>
              <th>Timestamp</th>
              <th>Offer Include</th>
          </tr>
        </thead>

        <tbody>
          <?php

            foreach ($arr_all as $key) {

          ?>
          <tr>
            <td><?php echo $key['order_id']; ?></td>
            <td><?php echo $key['user_name']; ?></td>
            <td><?php echo $key['fname']; ?></td>
            <td><?php echo $key['quantity']; ?></td>
            <td><?php echo '₹'.$key['price']; ?></td>
            <td><?php echo $key['timestamp']; ?></td>
            <td><?php echo $key['offer']; ?></td>
          </tr>

          <?php } ?>
         
        </tbody>
      </table>
	</div>
</div>

<?php require('layout/about-modal.php'); ?>
<?php require('layout/footer.php'); ?>