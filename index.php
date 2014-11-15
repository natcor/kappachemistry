<!DOCTYPE html>
<html lang="en">
<head>
	<title>Kappa Chemistry</title>
	<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
	<link rel="icon" href="/favicon.ico" type="image/x-icon">
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
     	   <!--<div class = "navbar">
     		   <a href = "#" class = "link">Contact</a>
     		   <a href = "#" class = "link">About</a>
     		   <a href = "#">Examples</a>
     	   </div>
		   -->
        <div class="cover-container">
     	 
          <div class="inner cover">
            <h1 class="cover-heading text-center">Kappa Chemistry</h1>
<?php
//Create options to be preset into the search box
$options = array('Enter Equation (e.g. AgNO3 + BaCl2)', 'Enter Equation (e.g. KCl + AgNO3)', 'Enter Equation (e.g. K2SO4 + AgNO3)', 'Enter Equation (e.g. Na3PO4  + Pb(NO3)2 )', 'Enter Equation (e.g. NaOH + H2SO4)', 'Enter Equation (e.g. H2 + O2)', 'Enter Equation (e.g. C + O2)', 'Enter Equation (e.g. Mg + O2)', 'Enter Equation (e.g. HCl + KOH)');
$num = rand(0, count($options) - 1);

$title = 'An Algorithmic Chemical Equation Predictor';

//Start the session and set up the variables
session_start();
$_SESSION['work'] = array(); //Variable to hold work to be shown
$_SESSION['errors'] = array();  //Variable to hold errors throughout
$_SESSION['transitions'] = array(); //Variable to hold charges of metals that can take more than one charge
$_SESSION['failedEquation'] = ''; //Variable to hold failed equation to submit for later review

//Require access to php pages with functions
require('main_functions.php');
require('included_functions.php');
require('periodic_table.php');
require('matrix_functions.php');

//require('mysqli_connect.php');

//Check for form submission
if($_SERVER['REQUEST_METHOD'] == 'POST'){
	
	//Ensure that the user entered something
	if(strlen($_POST['equation']) < 2){
		
		$_SESSION['errors'][] = 'Enter valid reactants, silly.'; 
	}

	//Remove any reaction arrows from equation and run it through the check precipitation function
	$found = false;
	if(empty($_SESSION['errors'])){
		
		$precipResult = getPrecipitation(returnReactants($_POST['equation']));
		$synthesisResult = getSynthesis(returnReactants($_POST['equation']));
		$acidResult = getAcidBase(returnReactants($_POST['equation']));
		$combustionResult = getCombustion(returnReactants($_POST['equation']));
		$results = array($precipResult, $acidResult, $synthesisResult, $combustionResult);
		$print = formatEquation($_POST['equation']) . ' --> No Reaction';
		foreach($results as $result){
			if($result !== false){
				$print = $result;
				$found = true;
				break;
			}
		}
		if(!$found){
			$title = 'Forever and Always a Fractal of Bad Design';
			/** Fractal > Fragment. Srsly though use a graphic arrow instead of --> and change the bullet point formatting
			 * (bigger font, no weird blue arrow). then it will look GUCCI BY RONE **/
			  
		}
	}
}

?>
	    <p class="lead text-center"><?php echo $title ?></p>
            <p class="lead">
		<form action = 'index.php' method = 'post' autocomplete="off" id = 'form'>
		<div class="form-group col-md-10 col-md-offset-1">
		<input type = "text" class = "form-control" name = "equation" id = "equation" placeholder = "<?php echo $options[$num] ?>" value = "<?php
		 if(isset($_POST['equation'])){
		echo $_POST['equation'];
		}?>" />
		</div>
		
		</form>
	    </p>
	    


    <!-- / taken from bs -->

<?php
if(isset($found)){
	
	if(!$found){
		if(!empty($_SESSION['errors'])){ //If there are items in the errors array
			$_SESSION['errors'] = array_unique($_SESSION['errors']);
			echo '<div id = "error"><div class = "lead text-center" style="margin-top: 40px;">The following error(s) occured: <p class="error">';
			foreach($_SESSION['errors'] as $error){
				echo '' . $error . '<br />'; 
			}
			echo '</p>Please fix and re-submit.
			</div></div>';
		}else{
			echo '<div id = "error"><div class = "lead text-center" style="margin-top: 40px;">The following error(s) occured: <p class="error">';
			echo 'As of yet, we do not support that type of reaction. Check back soon!';
			
			echo '</p>Please fix and re-submit.
			</div></div>';
			
			//Submit equation to cloud
			//Make sure the equation is non-malicious - NOTE: use prepared statements when you are feeling less lazy∑
			$e = mysqli_real_escape_string($dbc, trim($_SESSION['failedEquation']));
			$q = 'INSERT INTO failedEquations (equation, date) VALUES("' . $e . '", NOW())';
			$r = mysqli_query($dbc, $q);
		}
	}else{ //Has a result
		echo "<div id = 'results'><p class=\"text-center\" style=\"color: #e7e6fa; font-size: 23px;\">$print</p></div>";
		if(count($_SESSION['work']) > 0){
			$_SESSION['work'] = array_filter(array_unique($_SESSION['work']));
			echo '<div><ul class = "work" style = "display: table; margin: 0 auto;">';
			foreach($_SESSION['work'] as $step){
				echo '<li>' . $step . '</li>';
			}
		}
		echo "</ul></div>";
	}
}
	

//Unset all the session variables
session_unset();

?>


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
