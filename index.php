<?php

//Create options to be preset into the search box
$options = array('Enter Equation (e.g. AgNO3 + BaCl2)', 'Enter Equation (e.g. KCl + AgNO3)', 'Enter Equation (e.g. K2SO4 + AgNO3)');
$num = rand(0, count($options) - 1);

//Start the session and set up the variables
session_start();
$_SESSION['work'] = array(); //Variable to hold work to be shown
$_SESSION['errors'] = array();  //Variable to hold errors throughout
$_SESSION['transitions'] = array(); //Variable to hold charges of metals that can take more than one charge

?>


<!DOCTYPE html>
<html lang="en">
<head>
	<title>Kappa Chemical</title>
	<!--BS CDN--->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" />
	<!-- stuff in main.css will override the default bs stylesheet -->
	<link rel="stylesheet" href="main.css" type="text/css" media="screen" />
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
	  <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
	  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->
</head>
<body>
	<!-- taken from bs -->
    <div class="site-wrapper">

      <div class="site-wrapper-inner">

        <div class="cover-container">

          <div class="inner cover">
            <h1 class="cover-heading">Kappa Chemistry</h1>
	    <p class="lead">No longer a fractal of bad design</p>
            <p class="lead">
		<form action = 'index.php' method = 'post' autocomplete="off">
		<div class="form-group col-md-10 col-md-offset-1">
		<input type = "text" class = "form-control" name = "equation" id = "equation" placeholder = "<?php echo $options[$num] ?>" value = "<?php
		//don't think u need this: if(isset($_POST['equation'])){
		echo $_POST['equation'];
		//}?>" />
		</div>
		<div class="form-group">
		<button type="submit" name="submit" class="btn btn-lg btn-default" value="Solve">Solve</button>
		</div>
		</form>
	    </p>


    <!-- / taken from bs -->

<?php
$pageTitle = 'Home';

//Require access to php pages with functions
require('main_functions.php');
require('included_functions.php');
require('periodic_table.php');

//Check for form submission
if($_SERVER['REQUEST_METHOD'] == 'POST'){
	
	//Ensure that the user entered something
	if(strlen($_POST['equation']) < 2){
		
		$_SESSION['errors'][] = 'Enter valid reactants, silly.'; 
	}

	//Remove any reaction arrows from equation and run it through the check precipitation function
	if(empty($_SESSION['errors'])){
		$precipResult = getPrecipitation(returnReactants($_POST['equation']));
		$acidResult = getAcidBase(returnReactants($_POST['equation']));
	}
	
	if(!empty($_SESSION['errors'])){ //If there are items in the errors array
		echo '<div class = "lead" style="margin-top: 40px;">The following error(s) occured: <p class="error">';
		foreach($_SESSION['errors'] as $error){
			echo '' . $error . '<br />'; // UM SO I COULDNT GET LIST ELEMENTS TO NOT APPEAR ATROCIOUS SO THEYRE GONE
		}
		echo '</p>Please fix and re-submit.
		</div>';
	
	}else{ //No Errors	
		echo "<div id = 'results'><b>$precipResult</b></br></br><b>$acidResult</b>";
		if(count($_SESSION['work']) > 0){
			$_SESSION['work'] = array_filter(array_unique($_SESSION['work']));
			echo '</br><div id = "work_wrap"><h2 class = "header">Explanation</h2><ul class = "work">';
			foreach($_SESSION['work'] as $step){
				echo '<li>' . $step . '</li>';
			}
		}
		echo "</ul></div></div>";
	}
}

//Unset all the session variables
session_unset();

?>


          </div>

          <div class="mastfoot">
            <div class="inner">
              <p>Whatever you want to go here</p>
            </div>
          </div>

        </div>

      </div>

    </div>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
<!--IE10 viewport workaround (probz gonna want 2 have this file locally)-->
<script src="http://getbootstrap.com/assets/js/ie10-viewport-bug-workaround.js"></script>

</body>
</html>
