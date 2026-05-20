<?php require('layout/header.php'); ?>
<?php require('layout/topnav.php'); ?>
<?php require('layout/left-sidebar-long.php'); ?>
<?php require('layout/left-sidebar-short.php'); ?>


<?php

require __DIR__ . '/../backends/connection-pdo.php';

$sql = 'SELECT id,name FROM categories';

$query  = $pdoconn->prepare($sql);
$query->execute();
$arr_all = $query->fetchAll(PDO::FETCH_ASSOC);



?>


<div class="content">

	<div>
		<h3 style="padding-top:35px;">Add Food Item</h3>
	</div>


    <div class="center" style="padding: 40px;margin:20px;">

        <form action="../backends/admin/food-add.php" method="post" enctype="multipart/form-data">

            <?php

            if (isset($_SESSION['msg'])) {
                echo '<div class="row" style="background: #155724; color: white; border-radius:10px;">
                <div class="col s12">
                    <h6>'.$_SESSION['msg'].'</h6>
                    </div>
                </div>';
                unset($_SESSION['msg']);
            }

            ?>

            <div class="row">
                <div class="col s6" style="">
                            <div class="input-field">
                            <input id="name" name="name" type="text" class="validate" style="width: 70%">
                            <label for="name"><b>Food Name :</b></label>
                            </div>
                </div>
                <div class="col s6" style="">
                            <div class="input-field" style="width: 90%">
						    <select name='category' style="display:none">
						      <?php 

						      		foreach ($arr_all as $key) {
						      			echo '<option value="'.$key['id'].'">'.$key['name'].'</option>';
						      		}
						      ?>
						    </select>
						    <label><b>Categories :</b></label>
						  </div>
                </div>
            </div>

            <div class="row">
                <div class="col s8">

                <div class="input-field">
                <input id="desc" name="desc" type="text" class="validate" style="width: 70%">
                <label for="desc"><b>Description :</b></label>
                </div>
                
                </div>
            
            </div>

            <div class="row">
        
                <div class="col s6">

                <div class="input-field">
                <input id="price" name="price" type="text" class="validate" onkeypress='return removechar(event)' style="width: 80%">
                <label for="price" style="margin-right:10px;"><b>Price in ₹ :</b></label>
                </div>
                
                </div>
            
            </div>


            <div class="row">
                <div class="col s6">

                <div class="input-field">
                <input id="fimg" name="fimg" type="file" class="validate">
                <label for="fimg"><b>Insert Image:</b></label>
                </div>
                
                </div>
            </div>
            <div class="row" style="margin-top:20px;">
                <div class="col s12">

                <div class="input-field">
                    <input type="radio" name="data_status" value="Active" checked style="height:20px; width:20px; vertical-align: middle;"> Active
                    <input type="radio" name="data_status" value="Deactive" style="height:20px; width:20px; vertical-align: middle;margin-left:20px;"> Deactive
                </div>
                
                </div>
            </div>

            <div class="row">
                <div class="col s12">
                    <div class="right" style="padding: 15px 10px;">
                        <a href="food-list.php" class="waves-effect waves-light btn">Dismiss</a>
                    </div>
                    <div class="right" style="padding: 15px 20px;">
                        <button type="submit" class="waves-effect waves-light btn">Add New</button>
                    </div>
                </div>
            </div>

        </form>


    </div>

</div>

<?php require('layout/about-modal.php'); ?>
<?php require('layout/footer.php'); ?>