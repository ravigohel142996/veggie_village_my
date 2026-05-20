<section class="fnavbar">
		<div class="navbar-fixed">
		<nav>
		    <div class="nav-wrapper">
		      <a href="index.php" class="brand-logo">Veggie Village</a>
		      <a href="#" data-target="mobile-demo" class="sidenav-trigger"><i class="material-icons">menu</i></a>
		      <ul class="right hide-on-med-and-down">
		        <li><a href="index.php" class="hvr-grow">Home</a></li>
		        <li><a href="about-veggie-village.php" class="hvr-grow">About Us</a></li>
		        <li><a href="food-categories.php" class="hvr-grow">Categories</a></li>
		        <li><a href="foods.php" class="hvr-grow">Foods</a></li>
		        <li><a href="#" class="hvr-grow" onclick="toggleModal('Contact Info', 'You can contact us directly by calling to this number +1 602-944-1088. Check the bottom Footer Section of the website for more info.');">Contact</a></li>
		        
		        <?php

		        	if (isset($_SESSION['user'])) {
		        		echo '<li><a href="#" class="hvr-grow">Hi, '.$_SESSION['user'].'</a></li>
		        		<li><a href="logout.php" class="hvr-grow">Logout</a></li>';
		        	} else {
		        		echo '<li><a href="#" class="hvr-grow modal-trigger" data-target="modal1">Login</a></li> 
		        		<li><a href="#" class="hvr-grow modal-trigger" data-target="modal2">Register</a></li>';
		        	}

		        ?>
		        
		      </ul>
		    </div>
		  </nav>
		</div>

		<ul class="sidenav" id="mobile-demo">
    <li>
        <a href="index.php">
            <i class="material-icons">home</i> Home
        </a>
    </li>
    <li>
        <a href="about-veggie-village.php">
            <i class="material-icons">info</i> About Us
        </a>
    </li>
    <li>
        <a href="food-categories.php">
            <i class="material-icons">category</i> Categories
        </a>
    </li>
    <li>
        <a href="foods.php">
            <i class="material-icons">fastfood</i> Foods
        </a>
    </li>
    <li>
        <a href="#" onclick="toggleModal('Contact Info', 'You can contact us at +1 602-944-1088. Check the footer for more details.');">
            <i class="material-icons">contact_phone</i> Contact
        </a>
    </li>

    <?php if (isset($_SESSION['user'])) { ?>
        <li><a href="#"><i class="material-icons">account_circle</i> Hi, <?php echo $_SESSION['user']; ?></a></li>
        <li><a href="logout.php" class="waves-effect waves-light btn side-btn"><i class="material-icons">logout</i>Logout</a></li>
    <?php } else { ?>
        <li><a href="#" class="modal-trigger waves-effect waves-light btn side-btn" data-target="modal1"><i class="material-icons">login</i> Login</a></li>
        <li><a href="#" class="modal-trigger waves-effect waves-light btn side-btn" data-target="modal2"><i class="material-icons">person_add</i> Register</a></li>
    <?php } ?>
</ul>
	</section>