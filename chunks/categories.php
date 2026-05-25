
<?php

require __DIR__ . '/../backends/connection-pdo.php';

$sql = 'SELECT * FROM categories WHERE data_status = "Active"';

$query  = $pdoconn->prepare($sql);
$query->execute();
$arr_all = $query->fetchAll();

?>
<section class="fcategories">
    <div class="container">
        <div class="section white center">
            <h3 class="header" style="font-weight: 500;">Categories</h3>
        </div>

        <?php if (count($arr_all) == 0) { ?>
            <div class="section gray center">
                <img class="activator" src="images/Designer (10).jpeg" style="width:100%;height:450px;border-radius: 10px;">
            </div>
        <?php } else { ?>

        <div class="row">
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">

                <?php foreach ($arr_all as $row) { ?>
                    <div class="card" style="display: flex; flex-direction: column; min-height: 450px;">

                        <div class="card-image waves-effect waves-block waves-light">
                            <img class="activator" src="images/<?php echo $row['image']; ?>" style="width:100%; height:200px; object-fit: cover;">
                        </div>

                        <div class="card-content" style="display: flex; flex-direction: column; flex-grow: 1;">
                            <span class="card-title activator grey-text text-darken-4" style="font-weight: bold;"><?php echo $row['name']; ?><i class="material-icons right">more_vert</i></span>
                            <p style="font-size: 17px; font-weight: 500; flex-grow: 1;"><?php echo $row['short_desc']; ?></p>

                            <div style="text-align: center; padding-top: 15px;">
                                <a href="foods.php?id=<?php echo $row['id']; ?>" class="waves-effect waves-light btn select-cat"style="background: #3E7B27 !important; border-radius: 20px; color: #E6DD3B; font-weight: bold;">Select Categories &raquo;</a>
                            </div>
                        </div>

                        <div class="card-reveal">
                            <span class="card-title grey-text text-darken-4"><?php echo $row['name']; ?><i class="material-icons right">close</i></span>
                            <p><?php echo $row['long_desc']; ?></p>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>

        <?php } ?>
    </div>
</section>