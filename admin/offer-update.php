<?php require('layout/header.php'); ?>
<?php require('layout/topnav.php'); ?>
<?php require('layout/left-sidebar-long.php'); ?>
<?php require('layout/left-sidebar-short.php'); ?>

<?php

require __DIR__ . '/../backends/connection-pdo.php';

if (isset($_REQUEST['id'])) {

	$sql = "SELECT * FROM offers WHERE id = '".$_REQUEST['id']."'";
	
} else {

	$sql = "SELECT * FROM offers";

}

$query  = $pdoconn->prepare($sql);
$query->execute();
$arr_all = $query->fetch();
?>

<div class="content">

	<div>
		<h3 style="padding-top:35px;">Update Offers</h3>
	</div>


    <div class="center" style="padding: 40px;margin:20px;">

        <form action="../backends/admin/offer-update.php?id=<?php echo $_REQUEST['id']; ?>" method="post" enctype="multipart/form-data">

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
                <div class="col s6">
                    <div class="input-field">
                        <input id="title" name="title" type="text" class="validate" style="width: 70%" value="<?php echo $arr_all['title']; ?>">
                        <label for="title"><b>Offer Title :</b></label>
                    </div>
                </div>
                <div class="col s6">
                    <div class="input-field">
                        <input id="discount" name="discount" type="text" class="validate" style="width: 70%" onkeypress='return removechar(event)' value="<?php echo $arr_all['discount']; ?>">
                        <label for="discount"><b>Discount in % :</b></label>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col s12">

                    <div class="input-field">
                        <input id="desc" name="desc" type="text" class="validate" style="width: 70%" value="<?php echo $arr_all['description']; ?>">
                        <label for="desc"><b>Description :</b></label>
                    </div>
                
                </div>
            </div>
            <div class="row">
                <div class="col s6">
                    <div class="input-field">
                        <input id="s_date" name="s_date" type="date" class="validate" style="width: 70%" min="<?php echo date('Y-m-d'); ?>" value="<?php echo $arr_all['start_date']; ?>">
                        <label for="s_date"><b>Start Date :</b></label>
                    </div>
                </div>
                <div class="col s6">
                    <div class="input-field">
                        <input id="e_date" name="e_date" type="date" class="validate" style="width: 70%" min="<?php echo date('Y-m-d'); ?>" value="<?php echo $arr_all['end_date']; ?>">
                        <label for="e_date"><b>End Date :</b></label>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col s6">
                    <div class="input-field">
                        <input id="promo_code" name="promo_code" type="text" class="validate" style="width: 70%" value="<?php echo $arr_all['promo_code']; ?>">
                        <label for="promo_code"><b>Promo_Code :</b></label>
                    </div>
                </div>
                <div class="col s6">
                    <div class="input-field" style="width: 90%">
						<select name='status' style="display:none">
                            <option value="Active" <?php if($arr_all['status'] == 'active'){ echo 'selected';}?>>Active</option>
                            <option value="Upcoming" <?php if($arr_all['status'] == 'Upcoming'){ echo 'selected';}?>>Upcoming</option>
                            <option value="expired" <?php if($arr_all['status'] == 'expired'){ echo 'selected';}?>>expired</option>
						</select>
						<label><b>Status :</b></label>
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
                    <input type="radio" name="data_status" value="Active" checked style="height:20px; width:20px; vertical-align: middle;" <?php if($arr_all['data_status'] == "Active"){echo "checked";}?>> Active
                    <input type="radio" name="data_status" value="Deactive" style="height:20px; width:20px; vertical-align: middle;margin-left:20px;" <?php if($arr_all['data_status'] == "Deactive"){echo "checked";}?>> Deactive
                </div>
                
                </div>
            </div>

            <div class="row">
                <div class="col s12">
                    <div class="right" style="padding: 15px 10px;">
                        <a href="offer-list.php" class="waves-effect waves-light btn">Dismiss</a>
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