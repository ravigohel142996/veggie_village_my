<?php 
require __DIR__ . '/../backends/connection-pdo.php';
$viewsCount = 0;

try {
  $q = "SELECT view_count FROM page_views WHERE id = ?";
  $que = $pdoconn->prepare($q);
  $que->execute([1]);
  $views = $que->fetch();
  if (is_array($views) && isset($views['view_count'])) {
    $viewsCount = (int) $views['view_count'];
  }
} catch (Throwable $e) {
  vv_log_exception($e);
}
?>
<section class="ffooter">
		<footer class="page-footer">
          <div class="container">
            <div class="row">
              <div class="col l6 s12">
                <h5 style="color: #E6DD3B;">Contact US</h5>
                <p class="grey-text text-lighten-4"><span style="font-weight:bold;">Veggie Village</span><br><br>1255 E Northern Ave, Phoenix, AZ 85020, United States</p>
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3325.0249151446264!2d-112.05688724416454!3d33.55272898115501!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x872b6d5dea27977f%3A0x33171489d14788c5!2sVeggie%20Village!5e0!3m2!1sen!2sin!4v1741842048474!5m2!1sen!2sin" width="300" height="200" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                <p class="grey-text text-lighten-4">Phone : +1 602-944-1088</p>
              </div>
              <div class="col l4 offset-l2 s12">
                <h5 style="color: #E6DD3B;">Social Media Links</h5>
                <ul style="font-size:17px;" class="fiul">
                  <li><a class="text-lighten-3 fifacebook" href="https://www.facebook.com/veggievillagelincoln/" target="_blank"><i class="fa fa-facebook-square"></i> Facebook</a></li>
                  <li><a class="text-lighten-3 fiinsta" href="https://www.instagram.com/explore/locations/1221800887854542/veggie-village/" target="_blank"><i class="fa fa-instagram"></i> Instagram</a></li>
                  <li><a class="text-lighten-3 fitwitter" href="https://x.com/VillageVeggie" target="_blank"><i class="fa fa-twitter-square"></i> Twitter</a></li>
                  <li><a class="text-lighten-3 fiwhatsapp" href="https://whatsapp.com/channel/0029Vaj7x228aKvF8KjTdQ3Q" target="_blank"><i class="fa fa-whatsapp"></i> Whatsapp</a></li>
                </ul>
              </div>
              <div class="col l4 offset-l2 s12">
                <h5 style="color: #E6DD3B;text-align:center;">Views 👀</h5>
    <p style="font-size:18px;">🌟 Total <strong><?php echo $viewsCount; ?></strong> users have visited this website! 🌟</p>
    <p>🎉 Thanks for being a part of our journey! 🚀</p>
              </div>
            </div>
          </div>
          <div class="footer-copyright">
            <div class="container">
            © <?php echo date('Y');?> Copyright @ veggie village
            <a class="grey-text text-lighten-4 right" href="#!">Made in India with <span><i class="tiny material-icons" style="color:red;">favorite</i></span></a>
            </div>
          </div>
        </footer>
	</section>