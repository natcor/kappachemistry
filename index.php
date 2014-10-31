<?php
$options = array('Enter Equation (e.g. AgNO3 + BaCl2)', 'Enter Equation (e.g. KCl + AgNO3)', 'Enter Equation (e.g. K2SO4 + AgNO3)');
$num = rand(0, count($options) - 1);

session_start();

$_SESSION['work'] = array();

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
$page_title = 'Home';

//Set error reporting
//error_reporting(E_ALL & ~E_NOTICE);

//Require page that contains funcitons used in parsing the equation
require('helper_functions2.php');
//Require access to periodic table
require('periodic_table.php');

//Check for form submission
if($_SERVER['REQUEST_METHOD'] == 'POST'){
	
	//Create an array to place any errors in
	$errors = array();
	
	$e = ''; //Variable that holds reaction text
	
	//Validate entry - it does not matter at this point if it is non-malicious since it is not being placed into a database
	if(empty($_POST['equation'])){
		$errors[] = 'Please enter an equation. I\'m not about that empty equation life.';
	}else{
		$e = ' ' . $_POST['equation'];
	}
	
	//Check if equation has reaction sign
	$possible_signs = array('-->', '->', '>', '=', 'goes to', 'to');
	
	//Loop through all the possible reaction signs to check if any of them are present
	$count = 0;
	$reaction_sign = ''; //Value that holds which type of reaction error user typed
	
	foreach($possible_signs as $sign){
		if(strpos($e, $sign)){
			$reaction_sign = $sign;
			break;
		};
	}
	
	
		
	//Split the string into to parts at the reaction arrow
	if($reaction_sign != ''){
		$e_split = explode($reaction_sign, $e);
	}else{
		$e_split = array($e, '');
	}
	
	//If there is nothing on the right side, predict the products
	if(strlen(trim($e_split[1])) == 0){
		$result = checkPrecip($e_split[0]);
		
		if($result[0] != -1){
			$safety = 0;
			while( (!is_numeric(strpos($result[0], '<img src="reaction_arrow.png"/>'))) && ($safety < 25)){
				$safety++;
				$result = checkPrecip($e, $result[0], $result[1]);
			}
			if($safety >= 24){
				$errors[] = 'The equation you have entered is impossible to balance. Please review for errors.';
			}
		}		
	}
	
	if(!empty($errors)){ //If there are items in the errors array
		echo '<div class = "error"><p>The following error(s) occured: </p><ul>';
		foreach($errors as $error){
			echo '<li>' . $error . '</li>';
		}
		echo '</ul><p>Please fix and re-submit.</p>
		</div>';
		//exit;
	}else{ //No Errors	
		echo "<div id = 'results'><b>$result[0]</b>";
		if(count($result[1]) > 0){
			$result[1] = array_unique($result[1]);
			$result[1] = array_filter($result[1]);
			echo '</br><div id = "work_wrap"><h2 class = "header">Explanation</h2><ul class = "work">';
			foreach($result[1] as $step){
				echo '<li>' . $step . '</li>';
			}
		}
		echo "</ul></div></div>";
	}
	
	
}
?>
</div>
</div>
</div>
</body>
</html>
