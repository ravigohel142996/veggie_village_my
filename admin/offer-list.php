<?php

require __DIR__ . '/../backends/connection-pdo.php';

$sql = 'SELECT * FROM offers ORDER BY id DESC';

$query  = $pdoconn->prepare($sql);
$query->execute();
$arr_all = $query->fetchAll(PDO::FETCH_ASSOC);
?>


<?php require('layout/header.php'); ?>
<?php require('layout/topnav.php'); ?>
<?php require('layout/left-sidebar-long.php'); ?>
<?php require('layout/left-sidebar-short.php'); ?>

<div class="content">

	<div>
		<h3 style="padding-top:35px;">Offers</h3>
	</div>
  
  <?php

    if (isset($_SESSION['msg'])) {
        echo '<div class="section center" style="margin: 5px 35px;"><div class="row" style="background-color: #155724; color: white;border-radius:10px;">
        <div class="col s12">
            <h6>'.$_SESSION['msg'].'</h6>
            </div>
        </div></div>';
        unset($_SESSION['msg']);
    }

    ?>

	<div class="addnew" style="padding: 15px 25px;">
		<a href="offer-add.php" class="waves-effect waves-light btn">Add New</a>
	</div>
	
	<div class="section center" style="padding: 20px;">
		<table class="centered responsive-table offer-tb">
        <thead>
          <tr>
              <th>Status</th>
              <th>Title</th>
              <th>Discount</th>
              <th>Discription</th>
              <th>Promo_Code</th>
              <th>Start_Date</th>
              <th>End_Date</th>
              <th>Offer_Status</th>
              <th>image</th>
              <th>Edit</th>
              <th>Delete</th>
          </tr>
        </thead>

        <tbody>
          <?php

            foreach ($arr_all as $key) {

          ?>
          <tr>
            <td><?php echo $key['data_status']; ?></td>
            <td><?php echo $key['title']; ?></td>
            <td><?php echo $key['discount'].'%'; ?></td>
            <td><?php echo $key['description']; ?></td>
            <td><?php echo $key['promo_code']; ?></td>
            <td><?php echo $key['start_date']; ?></td>
            <td><?php echo $key['end_date']; ?></td>
            <td><?php echo $key['status']; ?></td>
            <td><img src="../images/<?php echo $key['image'];?>" height='100px' width='150px' style="border-radius:10px;"></td>
            <td><a href="offer-update.php?id=<?php echo $key['id']; ?>" class="btn blue lbtn">Edit</a></td>
            <td><a href="../backends/admin/offer-delete.php?id=<?php echo $key['id']; ?>" class="btn lbtn">Delete</a></td>
          </tr>

          <?php } ?>
         
        </tbody>
      </table>
	</div>
</div>

<?php require('layout/about-modal.php'); ?>
<?php require('layout/footer.php'); ?>