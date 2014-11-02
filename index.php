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

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Kappa Chemical</title>	
	<link rel="stylesheet" href="main.css" type="text/css" media="screen" />
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
</head>
<body>
<div class="outer">
<div class="middle">
<div class="inner">

<div class = "bubble">
<form action = 'index.php' method = 'post' autocomplete="off">
	<input type = "text" name = "equation" id = "equation" placeholder = "<?php echo $options[$num] ?>" value = "<?php
	if(isset($_POST['equation'])){
	echo $_POST['equation'];
	}?>"/>
	<input type = 'submit' name = 'submit' class = "myButton" value = "Solve" />
</form>
</div>


<?php
$pageTitle = 'Home';

//Require access to php pages with functions
require('main_functions.php');
require('included_functions.php');
require('periodic_table.php');

//Check for form submission
if($_SERVER['REQUEST_METHOD'] == 'POST'){
	
	//Ensure that the user entered something
	if(empty($_POST['equation'])){
		$_SESSION['errors'] = 'Please enter an equation. When you don\'t enter and equation, somewhere in Africa someone contracts ebola.';
	}
	
	//Remove any reaction arrows from equation and run it through the check precipitation function
	$result = getPrecipitation(returnReactants($_POST['equation']));
	echo $result;
	
	if(!empty($_SESSION['errors'])){ //If there are items in the errors array
		echo '<div class = "error"><p>The following error(s) occured: </p><ul>';
		foreach($_SESSION['errors'] as $error){
			echo '<li>' . $error . '</li>';
		}
		echo '</ul><p>Please fix and re-submit.</p>
		</div>';
	
	}else{ //No Errors	
		echo "<div id = 'results'><b>$result</b>";
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
</div>
</div>
</body>
</html>
