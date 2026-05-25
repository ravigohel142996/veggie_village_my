<?php require('layout/header.php'); ?>
<?php require('layout/topnav.php'); ?>
<?php require('layout/left-sidebar-long.php'); ?>
<?php require('layout/left-sidebar-short.php'); ?>

<?php

require __DIR__ . '/../backends/connection-pdo.php';

if (isset($_REQUEST['id'])) {

	$sql = 'SELECT * FROM categories WHERE id = "'.$_REQUEST['id'].'"';
	
} else {

	$sql = 'SELECT * FROM categories';

}

$query  = $pdoconn->prepare($sql);
$query->execute();
$arr_all = $query->fetch();



?>

<div class="content">

	<div>
		<h3 style="padding-top:35px;">Update Categories</h3>
	</div>


    <div class="center" style="padding: 40px;margin:20px;">

        <form action="../backends/admin/cat-update.php?id=<?php echo $_REQUEST['id']; ?>" method="post" enctype="multipart/form-data">

            <?php

            if (isset($_SESSION['msg'])) {
                echo '<div class="row" style="background-color: #155724;color:white;border-radius:10px;">
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
                            <input id="name" name="name" type="text" class="validate" style="width: 70%" value="<?php echo $arr_all['name'];?>">
                            <label for="name"><b>Category Name :</b></label>
                            </div>
                </div>
                <div class="col s6" style="">
                            <div class="input-field">
                            <input id="short_desc" name="short_desc" type="text" class="validate" style="width: 70%" value="<?php echo $arr_all['short_desc'];?>">
                            <label for="short_desc"><b>Short Description :</b></label>
                            </div>
                </div>
            </div>

            <div class="row">
                <div class="col s12">

                <div class="input-field">
                <input id="long_desc" name="long_desc" type="text" class="validate" style="width: 70%" value="<?php echo $arr_all['long_desc'];?>">
                <label for="long_desc"><b>Long Description :</b></label>
                </div>
                
                </div>
            </div>

            <div class="row">
                <div class="col s8">

                <div class="input-field">
                    <img src="../images/<?php echo $arr_all['image'];?>" style='height:150px;width:300px;'>
                    <input id="img" name="img" type="file" class="validate" style="border-radius:10px;">
                    <label for="img"><b>Insert Image:</b></label>
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
                        <a href="category-list.php" class="waves-effect waves-light btn">Dismiss</a>
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