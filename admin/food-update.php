<?php require('layout/header.php'); ?>
<?php require('layout/topnav.php'); ?>
<?php require('layout/left-sidebar-long.php'); ?>
<?php require('layout/left-sidebar-short.php'); ?>

<?php

require __DIR__ . '/../backends/connection-pdo.php';

if (isset($_REQUEST['id'])) {

	$sql = "SELECT food.data_status, food.id, food.cat_id, food.fname, food.description, food.image, food.price, categories.name FROM food LEFT JOIN categories ON food.cat_id = categories.id WHERE food.id = '".$_REQUEST['id']."'";
	
} else {

	$sql = "SELECT food.data_status, food.id, food.cat_id, food.fname, food.description, food.image, food.price, categories.name FROM food 
        LEFT JOIN categories ON food.cat_id = categories.id";

}

$query  = $pdoconn->prepare($sql);
$query->execute();
$arr_all = $query->fetch();

$query2  = $pdoconn->prepare("SELECT id,name FROM categories");
$query2->execute();
$arr_all2 = $query2->fetchAll();


?>


<div class="content">

	<div>
		<h3 style="padding-top:35px;">Update Food Item</h3>
	</div>


    <div class="center" style="padding: 40px;margin:20px;">

        <form action="../backends/admin/food-update.php?id=<?php echo $_REQUEST['id']; ?>" method="post" enctype="multipart/form-data">

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
                            <input id="name" name="name" type="text" class="validate" style="width: 70%" value="<?php echo $arr_all['fname'];?>">
                            <label for="name"><b>Food Name :</b></label>
                            </div>
                </div>
                <div class="col s6" style="">
                            <div class="input-field" style="width: 90%">
						    <select name='category' style="display:none">
						      <?php 

						      		foreach ($arr_all2 as $key) {
                                        if($key['id'] == $arr_all['cat_id']){
                                            echo '<option value="'.$key['id'].'" selected>'.$key['name'].'</option>';
                                        }else{
                                            echo '<option value="'.$key['id'].'">'.$key['name'].'</option>';
                                        }
						      		}
						      ?>
						    </select>
						    <label><b>Categories :</b></label>
						  </div>
                </div>
                
            </div>

            <div class="row">
                <div class="col s6">

                <div class="input-field">
                <input id="desc" name="desc" type="text" class="validate" style="width: 70%" value="<?php echo $arr_all['description'];?>">
                <label for="desc"><b>Description :</b></label>
                </div>
                
                </div>


                <div class="col s6" style="">
                    <div class="input-field">
                        <input id="price" name="price" type="text" class="validate" style="width: 70%;" value="<?php echo $arr_all['price'];?>" onkeypress='return removechar(event)'>
                        <label for="price"><b>Price :</b></label>
                    </div>
                </div>
                
            
            </div>

            <div class="row">
                <div class="col s8">

                <div class="input-field">
                <img src="../images/<?php echo $arr_all['image'];?>" style='height:150px;width:300px;'>
                <input id="fimg" name="fimg" type="file" class="validate">
                <label for="fimg"><b>Insert Image:</b></label>
                </div>
                
                </div>
            </div>

            <div class="row" style="margin-top:20px;">
                <div class="col s12">

                <div class="input-field">
                    <input type="radio" name="data_status" value="Active"  style="height:20px; width:20px; vertical-align: middle;" <?php if($arr_all['data_status'] == "Active"){echo "checked";}?>> Active
                    <input type="radio" name="data_status" value="Deactive" style="height:20px; width:20px; vertical-align: middle;margin-left:20px;" <?php if($arr_all['data_status'] == "Deactive"){echo "checked";}?>> Deactive
                </div>
                
                </div>
            </div>

            <div class="row">
                <div class="col s12">
                    <div class="right" style="padding: 15px 10px;">
                        <a href="food-list.php" class="waves-effect waves-light btn">Dismiss</a>
                    </div>
                    <div class="right" style="padding: 15px 20px;">
                        <button type="submit" class="waves-effect waves-light btn">Update</button>
                    </div>
                </div>
            </div>

        </form>


    </div>

</div>

<?php require('layout/about-modal.php'); ?>
<?php require('layout/footer.php'); ?>