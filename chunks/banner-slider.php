<?php 
require __DIR__ . '/../backends/connection-pdo.php';
$q = 'SELECT * FROM offers WHERE end_date < CURDATE() AND status != "expired"';
$q2 = 'SELECT * FROM offers WHERE start_date = CURDATE() AND status = "Upcoming"';
$u_query = 'UPDATE offers SET status = "expired" where end_date < CURDATE() AND status != "expired"';
$u_query2 = 'UPDATE offers SET status = "active" where start_date = CURDATE() AND status = "Upcoming"';
if($q>0){
    $query1 = $pdoconn->prepare($u_query);
    $query1->execute();
}
if($q2>0){
    $query2 = $pdoconn->prepare($u_query2);
    $query2->execute();
}



$sql = "SELECT * FROM offers WHERE CURDATE() BETWEEN start_date AND end_date AND data_status = 'Active' AND NOT status = 'expired' ORDER BY end_date ASC";
$query = $pdoconn->prepare($sql);
$query->execute();
$row = $query->rowCount();
$offers = $query->fetchAll();
?>


<section class="fslider">
		<div class="slider">
			<ul class="slides">
				<?php if($row == 0){
					echo '
					<li>
					<img src="images/food banner22.jpg">
					<div class="caption center-align black-text">  
					<h3 style="font-size: 3rem !important; font-style: bold !important; font-family: Bree Serif, serif;">Veggie Village - The Quality Food!</h3>  
					<h5 class="light black-text text-lighten-3"><strong>We deliver Quality. Try us and then buy us!</strong></h5>  
				  </div>  
				</li>
	
				<li>
					<img src="images/food banner21.jpg">
					<div class="caption center-align black-text">  
					<h3 style="font-size: 3rem !important; font-style: bold !important; font-family: Bree Serif, serif;">Quality Food at Your Door!</h3>  
					<h5 class="light black-text text-lighten-3"><strong>We deliver Quality And We re doing this for years!</strong></h5>  
				  </div>  
				</li>';
    
}else{
    foreach ($offers as $offer) {
        echo "<li class='offer-slide'>
        <!-- Image above the div -->
        <img src='images/$offer[image]' class='offer-img' style='width: 100%; display: block; border-radius: 10px;'>

        <div class='offer-caption center-align'>
        
            <h3 style='font-size: 3rem; font-weight: bold; font-family: Poppins, sans-serif; 
                    color: #ffcc00; text-transform: uppercase; margin-bottom: 10px;'>
                ⭐ {$offer['title']} ⭐
            </h3>  

            <h5 style='font-size: 1.5rem; color: #fff; font-family: Open Sans, sans-serif; margin-bottom: 10px;'>
                <i>{$offer['description']}</i>
            </h5>  

            <h5 style='color: #ff4444; font-size: 1.8rem; font-weight: bold; margin-bottom: 10px;'>
                🔥 Discount: {$offer['discount']}% OFF 🔥
            </h5>

            <h5 style='font-size: 1.5rem; font-weight: bold; color: #00ffcc; 
                    background: rgba(255, 255, 255, 0.2); padding: 10px; 
                    border-radius: 8px; display: inline-block; margin-bottom: 10px;'>
                Use Code: <span style='color: #ffcc00; text-transform: uppercase;'>
                    {$offer['promo_code']}
                </span>
            </h5>

            <h6 style='color: #ddd; font-size: 1.2rem; margin-top: 10px;'>
                📅 Offer Valid: <strong>{$offer['start_date']} to {$offer['end_date']}</strong>
            </h6>
        </div>
    </li>";
    }
}
?>

		    </ul>
		   
	  </div>
</section>