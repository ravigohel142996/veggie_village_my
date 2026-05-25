<?php require('layout/header.php'); ?>
<?php require('layout/topnav.php'); ?>
<?php require('layout/left-sidebar-long.php'); ?>
<?php require('layout/left-sidebar-short.php'); ?>


<?php

require __DIR__ . '/../backends/connection-pdo.php';

$sql = 'SELECT * FROM categories';

$query  = $pdoconn->prepare($sql);
$query->execute();
$arr_all = $query->fetchAll();



?>
						

<div class="content">

	<div>
		<h3 style="padding-top:35px;">Categories</h3>
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
		<a href="category-add.php" class="waves-effect waves-light btn">Add New</a>
	</div>
	
	<div class="section center" style="padding: 20px;">
		<table class="centered responsive-table">
        <thead>
          <tr>
              <th>Status</th>
              <th>Name</th>
              <th>Short Description</th>
              <th>Long Description</th>
              <th>Image</th>
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
            <td><?php echo $key['name']; ?></td>
            <td><?php echo $key['short_desc']; ?></td>
            <td><?php echo $key['long_desc']; ?></td>
            <td><img src="../images/<?php echo $key['image'];?>" height='100px' width='150px' style="border-radius:10px;padding-right:5px;padding-left:5px;"></td>
            <td><a href="category-update.php?id=<?php echo $key['id']; ?>" class="btn blue lbtn">Edit</a></td>
            <td><a href="../backends/admin/cat-delete.php?id=<?php echo $key['id']; ?>" class="btn lbtn">Delete</a></td>
          </tr>

          <?php } ?>
         
        </tbody>
      </table>
	</div>
</div>

<?php require('layout/about-modal.php'); ?>
<?php require('layout/footer.php'); ?>