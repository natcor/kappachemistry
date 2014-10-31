<?php

//TO FIX: make equations like K3PO4 (aq)  +  MgCl2 (aq) work, problem is the precipitate being Mg3(PO4)2 

//Problems like NaCl + AgCl not working

//Even balanced things like 2 K3PO4 (aq)  +  3 MgCl2 (aq) cause errors in the explanation

//AgNO3 + Na2S does not work

//Checks if a precipitation reaction is possible

function checkPrecip($reactants, $add = 'null'){
	
	//Change this value to TRUE to trigger print_r and echo statements throughout
	$debug = FALSE;
	$deep_debug = FALSE;
	
	$polyatomics = array('OH', 'NO3', 'CO3', 'PO4', 'NH4', 'SO4');
	
	//Store molecule form of equation in result variable
	$molecule_output = getWholeMolecules($reactants, $add);
	
	$edited_molecules = $molecule_output[0];
	$altered_reactants = $molecule_output[1];
	
	if($debug){
		echo '<p><b>Edited Molecules:</b> ';
		print_r($edited_molecules);
		echo '</p>';
		echo '<p>Altered Reactants: ';
		echo $altered_reactants;
		echo '</p>';
			
	}
	
	//Split into individual atoms/polyatomics
	$atoms = processMolecules($edited_molecules, 1);
	
	//Check the solubility of the atoms with eachother
	$precipitates = array();
	foreach($atoms as $left){
		foreach($atoms as $right){
			//If one is an anion and one is a cation, in the proper order
			if(orderIons($left, $right) == 0){
				if(!isSoluble($left, $right)){
					
					
					$precipitates[] = $left;
					$precipitates[] = $right;
				}
			}
		}
	}
	
	//Remove duplicates from precipitation array
	$precipitates = array_unique($precipitates);
	
	//If in debugging mode
	if($debug){
		echo '<p>Precipitate Array (Line 62: ';
		print_r($precipitates);
		echo '</p>';
	}
	
	//If there is only one potential precipitate
	if(count($precipitates) == 2){
		//Find first incidences of precipitates in atom array
		$c_ion = getTable($precipitates[0], 'ion');
		$a_ion = getTable($precipitates[1], 'ion');
		
		if(abs($c_ion) > abs($a_ion)){
			
			//Find ration between cation and anion if cation is larger charge
			$a_multiplier = $c_ion/abs($a_ion);
			
			if(is_float($a_multiplier)){
				$lcd = $c_ion * abs($a_ion);
				$a_multiplier = $lcd / abs($a_ion);
				$c_multiplier = $lcd / $c_ion;
			}else{
				$c_multiplier = 1;
			}
			
			$_SESSION['work'][] = $precipitates[0] . ' takes a ' . $c_ion . 'charge, ' . $precipitates[1] . ' takes a ' . $a_ion . ' charge. Therefore, the ration of cation:anion for the precipitate must be ' . $c_multiplier . ':' . $a_multiplier . '.';
			
			
		}else{
			//Find ration between cation and anion if anion is larger charge
			$c_multiplier = abs($a_ion)/abs($c_ion);
			
			if(is_float($c_multiplier)){
				$lcd = $c_ion * abs($a_ion);
				$c_multiplier = $lcd / $c_ion;
				$a_multiplier = $lcd / abs($a_ion);
			}else{
				$a_multiplier = 1;
			}
			
			$_SESSION['work'][] = $precipitates[1] . ' takes a ' . $a_ion . ' charge, ' . $precipitates[0] . ' takes a ' . $c_ion . ' charge. Therefore, the ration of cation:anion must be ' . $a_multiplier . ':' . $c_multiplier . '.';
			
		}
		
		//Count number of precipitates
		$ccount = 0;
		$acount = 0;
		
		foreach($atoms as $atom){
			if($atom == $precipitates[0]){
				$ccount++;
			}
			if($atom == $precipitates[1]){
				$acount++;
			}
		}
		
		//Debug statement
		if($debug){
			echo '</br>(Line 148) ccount: ' . $ccount;
			echo ' acount: ' . $acount;
			echo ' c_multiplier' . $c_multiplier;
			echo ' a_multiplier' . $a_multiplier;
			
		}
		
		//Counts how many moles of precipitate are formed
		$precipitate_count = 0;
		
		//Takes out the appropriate amount of precipitate from the atom array
		while(($ccount >= $c_multiplier) && ($acount >= $a_multiplier)){
			for($i = 0; $i < $c_multiplier; $i++){
				$cation_key = array_search($precipitates[0], $atoms);
				$atoms[$cation_key] = '';
			}
			for($i = 0; $i < $a_multiplier; $i++){
				$anion_key = array_search($precipitates[1], $atoms);
				$atoms[$anion_key] = '';
			}
			$precipitate_count++;
			
			//Recount the atoms
			$ccount = 0;
			$acount = 0;
		
			foreach($atoms as $atom){
				if($atom == $precipitates[0]){
					$ccount++;
				}
				if($atom == $precipitates[1]){
					$acount++;
				}
			}
		}
		
		//Debug statement
		if($debug){
			echo '</br>(Later Count Values, Line 186) ccount: ' . $ccount;
			echo ' acount: ' . $acount;
			
		}
		
		//Define products variable
		$products = '';
		
		//If there are still leftover reactants for the precipitate
		if( !(($ccount == 0) && ($acount == 0)) ){
			
			//Check if it is possible for a precipitate to form
			if(count($precipitates) > 0){
				
				//Needs to be balanced
				if($c_multiplier > $ccount){
					if($debug){
						echo "<p>Needs More $precipitates[0]";
					}
					$_SESSION['work'][] = 'Requires more ' . $precipitates[0] . ' to balance the reation.';
				
					return array($precipitates[0], $_SESSION['work']);
					
				}else{
					if($debug){
						echo "<p>Needs More $precipitates[1]";
					}
		
					$_SESSION['work'][] = 'Requires more ' . $precipitates[1] . ' to balance the reation.';
					return array($precipitates[1], $_SESSION['work']);
				}
			}
			
		}else{
			//Add precipitate to products array
			if($precipitate_count == 1){
				$precipitate_count = '';
			}
			//Reference variables
			$c_hold = $c_multiplier;
			$a_hold = $a_multiplier;
			
			if($c_hold == 1){
				$c_hold = '';
			}
			if($a_hold == 1){
				$a_hold = '';
			}
			
			//Add to products array
			$products = formatEquation($precipitate_count . $precipitates[0] . "<sub class = 'small'>$c_hold</sub>" . $precipitates[1] . "<sub class = 'small'>$a_hold</sub>") .'<sub class = "small">(s)</sub>';
		}
		
		$atoms = array_filter($atoms);
		$count = array_count_values($atoms);
		//print_r($count);
		foreach($count as $atom => $number){
			if($number == 1){
				$number = '';
			}
			
			//Get the charge of the ion, if appropriate
			$charge = getTable($atom, 'ion');
			if(getTable($atom, 'ion') > 0){
				$charge = '+' . getTable($atom, 'ion');
			}else if($charge == 0){
				$charge = '';
			}
			
			if(in_array($atom, $polyatomics)){
				$spot = strcspn($atom, '123456789');
				$sub = $atom{$spot};
				$atom = str_replace($sub, '<sub class = "small">' . $sub . '</sub>', $atom);
			}
			
			$products .= ' + ' . $number . $atom . '<sup class = "small">' . $charge . '</sup>' . '<sub class = "small">(aq)</sub>';
		}
		
		$string = formatEquation($altered_reactants) . ' <img src="reaction_arrow.png"/> ' . $products;
		
		$return_array = array($string, $_SESSION['work']);
		
		return $return_array;
		
	}
	if(count($precipitates == 0)){
		return array(formatEquation($altered_reactants) . '-->' . 'No Precipitation Reaction', $_SESSION['work']);
	}
	return -1;
}

//Takes input if two atoms and returns them in the correct charge ratios
function matchAtoms($cation, $anion){
	$atoms = array();
	
	//get ion information
	$c_ion = getTable($cation, 'ion');
	$a_ion = getTable($anion, 'ion');
	
	if(abs($c_ion) > abs($a_ion)){
		
		//Find ration between cation and anion if cation is larger charge
		$a_multiplier = $c_ion/abs($a_ion);
		
		if(is_float($a_multiplier)){
			$lcd = $c_ion * abs($a_ion);
			$a_multiplier = $lcd / abs($a_ion);
			$c_multiplier = $lcd / $c_ion;
		}else{
			$c_multiplier = 1;
		}
		
		$_SESSION['work'][] = $precipitates[0] . ' takes a ' . $c_ion . 'charge, ' . $precipitates[1] . ' takes a ' . $a_ion . ' charge. Therefore, the ration of cation:anion for the precipitate must be ' . $c_multiplier . ':' . $a_multiplier . '.';
		
		array_push($atoms, $cation . $c_multiplier, $anion . $a_multiplier); 
		
		
	}else{
		//Find ration between cation and anion if anion is larger charge
		$c_multiplier = abs($a_ion)/abs($c_ion);
		
		if(is_float($c_multiplier)){
			$lcd = $c_ion * abs($a_ion);
			$c_multiplier = $lcd / $c_ion;
			$a_multiplier = $lcd / abs($a_ion);
		}else{
			$a_multiplier = 1;
		}
		
		$_SESSION['work'][] = $precipitates[1] . ' takes a ' . $a_ion . ' charge, ' . $precipitates[0] . ' takes a ' . $c_ion . ' charge. Therefore, the ration of cation:anion must be ' . $a_multiplier . ':' . $c_multiplier . '.';
		
		array_push($atoms, $cation . $c_multiplier, $anion . $a_multiplier); 
		
	}
	
	return $atoms;
	
}

//Function that takes an input of a string of molecules and a split level. Split level 0 = split only on the molecular level. Split level 1 = split up things such as Cl2
function processMolecules($molecules, $level){
	
	//Right now polyatomics array needs to be added each time its used in a function - not economic. Could be included in periodic table file?
	$polyatomics = array('OH', 'NO3', 'CO3', 'PO4', 'NH4', 'SO4');
	
	//Array to hold atoms before there numbers (i.e. Cl2) are taken into account
	$temp_atoms = array();
	foreach($molecules as $molecule){
		
		//Push the individual atoms to the end of the array
		foreach($polyatomics as $poly){
			
			$pos = strpos($molecule, $poly);
			
			if($pos !== false){
				
				//Special case: if there is a polyatomic with subscript creates issues, like Ba(NO3)2.
				$num = substr($molecule, $pos + strlen($poly), $pos + strlen($poly) + 1);
				if(is_numeric($num)){
					array_push($temp_atoms, substr($molecule, $pos, ($pos + strlen($poly) + 1)) );
				}else{
					$num = '';
					array_push($temp_atoms, substr($molecule, $pos, $pos + strlen($poly)) );
				}
				$whole = $poly . $num;
				$molecule = str_replace($whole, '', $molecule);
			}
		}
		
		//Substrings past the first character, then searches for the next upercase letter to find the next atom.
		array_push($temp_atoms, substr($molecule, 0,  strcspn(substr($molecule, 1), 'ABCDEFGHIJKLMNOP') + 1), substr($molecule,  strcspn(substr($molecule, 1), 'ABCDEFGHIJKLMNOP') + 1));
	}
	
	$temp_atoms = array_filter($temp_atoms);
	
	
	//If the function has been called to split all the way
	if($level == 1){
		
		//Array to hold final atoms
		$atoms = array();
		foreach($temp_atoms as $atom){
			if(!in_array($atom, $polyatomics)){
			
				$last = substr($atom, -1, 1);
				if(is_numeric($last)){
					for($i = 0; $i < $last; $i++){
						$atoms[] = substr($atom, 0, -1);
					}
				}else{
					$atoms[] = $atom;
				}
			}else{
				$atoms[] = $atom;
			}
		}
		return $atoms;
	}
	return $temp_atoms;
}

function getWholeMolecules($reactants, $add){
	
	$polyatomics = array('OH', 'NO3', 'CO3', 'PO4', 'NH4', 'SO4');

	//Split reactants into molecules
	$molecules = explode('+', $reactants);
	
	//Array for if moles changed while balancing
	$altered_reactants = '';
	
	//Array for molecules seperated from coefficients
	$edited_molecules = array();

	foreach($molecules as $molecule){
		
		//Remove and parenthesis or spaces
		$molecule = str_replace('(', '', $molecule);
		$molecule = str_replace(')', '', $molecule);
		$molecule = str_replace(' ', '', $molecule);
		
		//For balancing purposes, increase the moles of one reactant specified in the function call
		if(is_numeric( (strpos($molecule, $add)) ) ){
		
			if(is_numeric(substr($molecule, 0, 1))){
				$num = substr($molecule, 0, 1);
				$num++;
				$molecule = $num . substr($molecule, 1);
			}else{
				$molecule = '2' . $molecule;
			}
		}
		
		//Add molecule to altered reactants array, there will be a trailing plus
		$altered_reactants .=  $molecule . ' + ';
		
		$ante = substr($molecule, 0, 1);
		
		//If the coefficient is a number  !NOTE! IF COEFFICIENT IS > 9 WILL NOT WORK! MUST FIX!
		if(is_numeric($ante)){	
			//Take off the coefficient and add it the appropriate number of times to new array
			for($i = 0; $i < (int)$ante; $i++){
				$edited_molecules[] = str_replace($ante, '', $molecule);
			}
			
		}else{
			$edited_molecules[] = $molecule;
		}
		
	}
	
	//Take off the trailing plus sign
	$altered_reactants = substr($altered_reactants, 0, -2);
	
	return array($edited_molecules, $altered_reactants);
}

function formatEquation($equation){
	$polyatomics = array('OH', 'NO3', 'CO3', 'PO4', 'NH4', 'SO4');
	$formatted = '';
	$molecules = explode('+', $equation);
	foreach($molecules as $molecule){
		$molecule = str_replace(' ', '', $molecule); 
		$ante = '';
		if(is_numeric(substr($molecule, 0, 1))){
			$ante = substr($molecule, 0, 1);
			$molecule = substr($molecule, 1);
		}
		
		//For polyatomics, fix both i.e. (NO3)3 subscripts both 3's
		foreach($polyatomics as $poly){
			$pos = strpos($molecule, $poly);
			if(is_numeric($pos)){
	
				$molecule .= ' ';
				if(is_numeric($molecule{$pos + strlen($poly)}) ){
					
					$num = $molecule{($pos + strlen($poly))};
					
					if($num <= 1){
						$num = '';
					}else{
						$molecule = str_replace(' ', '', $molecule); 
						$molecule = substr_replace($molecule, '<sub class = "small">' . $num . '</sub>', -1);
						$molecule = str_replace($poly, '(' . $poly . ')', $molecule);
					}
					
					$spot = strcspn($poly, '123456789');
					if(is_numeric($spot)){
						$sub = $poly{$spot};
						$changed = str_replace($sub, '<sub class = "small">' . $sub . '</sub>', $poly);
						$molecule = str_replace($poly, $changed, $molecule);
					}
					
				}else{
					$spot = strcspn($poly, '123456789');
					if(is_numeric($spot)){
						$sub = $poly{$spot};
						$changed = str_replace($sub, '<sub class = "small">' . $sub . '</sub>', $poly);
						$molecule = str_replace($poly, $changed, $molecule);
					}
				}
			}
		}
		
		//Fix regulars subscripts
		$spot = strcspn($molecule, '123456789');
		if(is_numeric($spot)){
			if(isset($molecule{$spot})){
				$sub = $molecule{$spot};
				if(!($molecule{($spot - 1)} == '>')){
					$string = '<sub class = "small">' . $sub . '</sub>';
					$molecule = substr_replace($molecule, $string, $spot, 1);
				}
			}
		}
	
		$formatted .= $ante . $molecule . ' + ';
		
		
	}
	
	return trim(substr($formatted, 0, -2));
}
function orderIons($first, $second){
	
	//Ensure it is not the same atom
	if($first == $second){
		return -1;
	}
	
	//Ensure they are not both anions
	if( (getTable($first, 'electronegativity') > 2) && (getTable($second, 'electronegativity') > 2) ){
		return -1;
	}
	
	
	//Ensure they are both not cations
	if( (getTable($first, 'electronegativity') < 2) && (getTable($second, 'electronegativity') < 2) ){
		return -1;
	}
	
	//Check the electronegativities. The atom that is less electronegative will be the cation
	if(getTable($first, 'electronegativity') < getTable($second, 'electronegativity')){
		return 0;
	}else{
		return 1;
	}
}

function isSoluble($cation, $anion){
	
	//Needs to be adjusted for varying cation charge i.e. Ba(OH)2 IS soluble but BaOH is NOT.
	
	$to_return = array();
	$halides = array('F', 'Cl', 'Br', 'I');
	$halide_exceptions = array('Cu', 'Pb', 'Hg', 'Ag');
	$soluble = array('Na', 'Li', 'K', 'Rb', 'NH4');
	$earth_metals = array('Be', 'Mg', 'Ca', 'Sr', 'Ba', 'Ra');

	if($anion == 'NO3'){ //All nitrates are soluble
	
		$_SESSION['work'][] = $cation . $anion . ' is soluble: All nitrates are soluble.';
		return true;
	}

	if( (in_array($cation, $soluble)) || (in_array($anion, $soluble)) ){ //If akali metal it is soluble
		
		$_SESSION['work'][] = $cation . $anion . ' is soluble: All alkali metals and ammonium salts are soluble';
		return true;
	}else{
		if(in_array($anion, $halides)){ //If a halide is the anion it is soluble, with exceptions 
			if(!in_array($cation, $halide_exceptions)){ //Check if cation is an exception to the halide solubility rule
				
				$_SESSION['work'][] = $cation . $anion . ' is soluble: Most halide salts are soluble (Exceptions: Cu, Pb, Hg<sub class = "small">2</sub>,
				 Ag)';
				 return true;
			}else{
				
				$_SESSION['work'][] = $cation . $anion . ' is insoluble: It is an exception to the halide salts are soluble rule.';
				return false;
				
			}
		}
	}
	
	return false;
}
?>
