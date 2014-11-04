<?php

//Takes the input of a reaction that may or may not have a reaction arrow or other symbol
//Returns only the reactant equation
function returnReactants($input){
	
	//Check if equation has reaction sign
	$possibleSigns = array('-->', '->', '>', '=', 'goes to', 'to', 'yields');
	
	//Loop through posible signs to check if one is present
	foreach($possibleSigns as $sign){
		if(strpos($input, $sign) !== false){
			
			//Return the input without the sign, and no spaces
			return trim(substr($input, 0, strpos($input, $sign)));
		}
	};
	
	//If no sign, return the reactants
	return $input;
}

//Converts and words to their corresponding atoms
function convertWords($equation){
	
	$conversions = array(
		'nitrate' => '(NO3)',
		'sulfate' => '(SO4)',
		'chromate' => '(CrO4)',
		'ammonium' => '(NH4)',
		'ammonia' => '(NH3)',
		'phosphate' => '(PO4)',
		'hydroxide' => '(OH)',
		'acetate' => '(C2H3O2)',
		'hydrogen' => 'H',
		'lithium' => 'Li',
		'sodium' => 'Na',
		'potassium' => 'K',
		'rubidium' => 'Rb',
		'caesium' => 'Cs',
		'cesium' => 'Cs',
		'francium' => 'Fr',
		'berylium' => 'Be',
		'magnesium' => 'Mg',
		'calcium' => 'Ca',
		'strontium' => 'Sr',
		'barium' => 'Ba',
		'radium' => 'Ra',
		'chloride' => 'Cl',
		'titanium' => 'Ti',
		'tungsten' => 'W',
		'manganese' => 'Mn',
		'iron' => 'Fe',
		'cobalt' => 'Co',
		'nickel' => 'Ni',
		'palladium' => 'Pd',
		'platinum' => 'Pt',
		'copper' => 'Cu',
		'silver' => 'Ag',
		'gold' => 'Au',
		'zinc' => 'Zn',
		'cadmium' => 'Cd',
		'mercury' => 'Hg',
		'aluminum' => 'Al',
		'boron' => 'B',
		'carbon' => 'C',
		'silicon' => 'Si',
		'tin' => 'Sn',
		'lead' => 'Pb',
		'bismuth' => 'Bi',
		'nitride' => 'N',
		'phosphide' => 'P',
		'oxide' => 'O',
		'sulfide' => 'S',
		'selenide' => 'Se',
		'flouride' => 'F',
		'chloride' => 'Cl',
		'bromide' => 'Br',
		'iodide' => 'I',
	);
	
	$changed = array();
	
	//Split reactants into molecules
	$molecules = explode('+', $equation);
	foreach($molecules as $molecule){
		$atoms = explode(' ', $molecule);
		foreach($atoms as &$atom){
			
			//Match anything between parenthesis (namely charges) and store the charge in $atom
			preg_match('/\((.*?)\)/', $atom, $matches);
			if(count($matches) > 1){
				if(is_numeric($matches[1])){
					$charge = $matches[1];
				}else{
					$charge = toArabic($matches[1]);
				}
					
				$atom = str_replace($matches[0], '', $atom);
			}else{
				$charge = null;
			}
			
			foreach($conversions as $conversion => $value){
				
				if(strpos($atom, $conversion) !== false){
					
					//Replace the text with the correct words
					$atom = str_replace($conversion, $value, $atom);
					
					//If a charge was given, put it into the session variable
					if($charge > 0){
						$_SESSION['transitions'][$atom] = $charge;
					}
				}
		
			}
		}
		
		$atoms = array_values(array_filter($atoms));
		
		if(isset($atoms[1])){
			$changed[] = matchCharges($atoms[0], $atoms[1]);
		}else{
			$changed[] = $atoms[0];
		}
		
	}
	
	return implode(' + ', $changed);
}

//Checks if the input is in a valid format with correct charges and ratios
function isValid($input){
	
	//Split the input into molecules without coefficients and remove duplictes so only distinct molecules are left
	$molecules = array_filter(array_unique(splitEquation($input, 2)));
	
	//Loop through and split each molecule into it's atoms
	foreach($molecules as $molecule){
		
		//Split array into individual atoms
		$atoms = array_filter(splitEquation($molecule));
		
		//Define variable to hold overall charge of molecule
		$netCharge = 0;
		
		//Value to hold metals found in the molecule that can take multiple charges (i.e. Cu)
		$multipleCharges = '';
		
		//Value to hold number of metal that can take multiple charges
		$multipleCount = 0;
		
		//Loop through each atom and add its charge to $netCharge. If entered correctly, charge should be zero for ionics.
		foreach($atoms as $atom){
			
			//If the atom can take more than one charge, store it to be looped through later
			if(gettype(getTable($atom, 'charge')) != 'integer'){
			
				$multipleCharges = $atom;
				$multipleCount++;
				
			}else{ //Atom has only one possible charge. Add charge to $netCharge.
				
				$netCharge += getTable($atom, 'charge');
			}
		}
		
		//If there was an atom with multiple charges, loop through it and check if any of its possible charges balance the molecule
		if($multipleCharges != ''){
			foreach(getTable($multipleCharges, 'charge') as $charge){
				
				//If the charge balances the molecule
				if($netCharge + $multipleCount * $charge == 0){
					$netCharge += $multipleCount * $charge;
					
					//Add the atom and charge to be used later
					$_SESSION['transitions'][$multipleCharges] = $charge;
					break;
				}
			}
		}
		//If the charge is not zero, set an error message and return false
		if($netCharge !== 0){
			$_SESSION['errors'][] = 'There seems to be something fishy with the equation you entered. Perhaps you never passed basic addition in middle school. That might be it. Check your goddam charges.';
			return false;
		}
	}
	
	//If did not return false before this, equation is OK.
	return true;
}

/*

Takes input of equation and a level and returns and array. Splits equation to a certain extent based on level. All returns are in the form of arrays.
	Level 1: split to molecules with coefficients. 
	Level 2: split to molecules without coefficients, but appropriate number of molecules (i.e. 2NaCl turns into NaCl, NaCl).
	Level 3: split to atoms but ignoring number (i.e. Cl2)
	Level 4 (default): split to atoms including numbers (i.e. Cl2 turns into Cl, Cl)

	Account solubility by default is set to false. If set to true, then it will take into account whether molecules are soluble and will not split them.

*/

function splitEquation($equation, $level = 4, $ignoreSolublity = true){

	//Strip all whitespace
	$equation = preg_replace('/\s+/', '', $equation);
	
	//Split reactants into molecules
	$molecules = explode('+', $equation);
	
	//If first level, return the array with unaltered molecules
	if($level == 1){
		return array_filter($molecules);
	}
	
	//Create a value to hold molecules after taking coefficients into account
	$adjustedMolecules = array();
	
	//Loop through unaltered molecules. If there is a coefficient, add appropriate amount.
	foreach($molecules as $molecule){
		
		//Define variable to hold coefficient
		$coefficient = '';
		
		//Ensure the entire antecedent is captured, no matter how many digits it is.
		$index = 0;
		while(is_numeric($molecule{$index})){
			$coefficient = substr($molecule, 0, $index+1);
			$index++;
		}
		
		if(is_numeric($coefficient)){	
			
			//Take off the coefficient and add the molecule appropriate number of times to new array
			for($i = 0; $i < $coefficient; $i++){
				$adjustedMolecules[] = preg_replace("/$coefficient/", '', $molecule, 1);
			}
		}else{
			//If there is no coefficient, just add the molecule once
			$adjustedMolecules[] = $molecule;
		}
	}

	//If second level, return array of adjusted molecules
	if($level == 2){
		return array_filter($adjustedMolecules);
	} 
	
	//Array to hold atoms before there numbers (i.e. Cl2) are taken into account
	$rawAtoms = array();
	
	//Split molecules into individual atoms based on location of next capital letter
	foreach($adjustedMolecules as $molecule){
			
		//Variable that holds value to be pushed into array later
		$toPush = '';
		
		//Push the individual atoms to the end of the array
		foreach(array_keys(getTable(null, null, 'polyatomics')) as $poly){
			
			//If no polyatomic ion is found in the molecule, check for one without parenthesis
			if(strpos($molecule, $poly) === false){
				
				//If the molecule is equal to the polyatomic ion without the parenthesis, add parenthesis
				$check = strtr($poly, array('(' => '', ')' => ''));
				if(strpos($molecule, $check) !== false){
					$molecule = str_replace($check, $poly, $molecule);
				}
			
			}
		
			//Create variable to hold position of polyatomic
			$pos = strpos($molecule, $poly);
		
			//If there is a polyatomic ion in the molecule
			if($pos !== false){
			
				//Special case: if there is a polyatomic with subscript at the end, like Ba(NO3)2, or (NH4)2O
				
				//Create value to hold number (if existant). If the polyatomic ion is not at 0, take one of the total to avoid index out of bounds
				if($pos == 0){
					$num = (strlen($molecule) > $pos + strlen($poly) ? $molecule{$pos + strlen($poly)} : null);
				}else{
					$num = (strlen($molecule) > $pos + strlen($poly) ? $molecule{$pos + strlen($poly)} : null);
				}
				
			
				//If the character after the polytomic is a number, ensure to push the number with the molecule
				if(is_numeric($num)){
					
					//If the polyatomic is not the first atom
					if($pos > 0){
						$toPush = substr($molecule, $pos, ($pos + strlen($poly) + 1));
					}else{
						$rawAtoms[] = substr($molecule, $pos, ($pos + strlen($poly) + 1));
					}
					
				}else{
					$num = '';
					if($pos > 0){
						$toPush = substr($molecule, $pos, $pos + strlen($poly));
					}else{
						$rawAtoms[] = substr($molecule, $pos, $pos + strlen($poly));
					}
					
					
				}
			
				//Replace the entire molecule including trailing number (if existant) so that it will not be added again
				$whole = $poly . $num;
				$molecule = str_replace($whole, '', $molecule);
			}
		}
		
		//Substrings past the first character, then searches for the next upercase letter to find the next atom.
		array_push($rawAtoms, substr($molecule, 0,  strcspn(substr($molecule, 1), 'ABCDEFGHIJKLMNOPQRSTUVWXYZ') + 1), substr($molecule,  strcspn(substr($molecule, 1), 'ABCDEFGHIJKLMNOPQRSTUVWXYZ') + 1));
		
		//Push the polyatomic afterward
		if($toPush != ''){
			$rawAtoms[] = $toPush;
		}
		
	}
	
	//Filter the raw atoms
	$rawAtoms = array_filter($rawAtoms);
	
	//If third level, return array of raw atoms 
	if($level == 3){
		return $rawAtoms;
	}
	
	//Array to hold fully split atoms
	$splitAtoms = array();
	
	//Split the atoms if the they have trailing numbers
	foreach($rawAtoms as $atom){
		
		//Get value for trailing character and check if it is a number
		$last = substr($atom, -1, 1);
		if(is_numeric($last)){ 
			//If it is a number, add the atom the appropriate number of times
			for($i = 0; $i < $last; $i++){
				$splitAtoms[] = substr($atom, 0, -1);
			}
		}else{
			$splitAtoms[] = $atom;
		}
	}

	//Return the split atoms array if level is not 1, 2, or 3
	return array_filter($splitAtoms);
}

//Takes an input of an anion and a cation atom. Returns a string of them combined in the appropriate ratios for their common charges
//Using Peter's better version an array with the actual subscripts for the anion and cation
function matchCharges($cation, $anion, $usePetersBetterVersion = false){
	
	//Get the common charges of the cation. If it is an element with multiple charges, find the correct charge from the global session variable
	if(is_numeric(getTable($cation, 'charge')) ){
		$cCharge = getTable($cation, 'charge');
	}else{
		$cCharge = $_SESSION['transitions'][$cation];
	}
	
	//Get the common charge of the anion. There are no anions that have multiple charges.
	$aCharge = getTable($anion, 'charge');
	
	//Find the least common multiple of the two charges, then divide that by the charge to get the multipliers of the atoms
	$cMult = abs(lcm($cCharge, $aCharge) / $cCharge);
	$aMult = abs(lcm($cCharge, $aCharge) / $aCharge);
	
	if($usePetersBetterVersion){
		return array('cation' => $cMult, 'anion' => $aMult);
	}
	
	//If the multipliers are one replace them with an empty string
	if($cMult == 1){
		$cMult = '';
	}
	if($aMult == 1){
		$aMult = '';
	}

	return "$cation$cMult$anion$aMult";
}

/*
	- Function takes a string as input (must have matchCharges peformed on it)
	- Returns boolean true/false
*/
function isSoluble($molecule, $showWork = false){
	
	//Split molecule into individual atoms, but keep the trailing numbers
	$atoms = array_values(splitEquation($molecule, 3));
	
	//Combine atoms array back into molecule -- ensure polyatomics get parenthesis put around them
	$molecule = implode(array_unique($atoms));
	
	//Solubility rules arrays
	$halide_exceptions = array('Cu', 'Pb', 'Hg', 'Ag');
	$soluble = array('(NH4)', '(NO3)', '(ClO3)', '(ClO4)', '(C2H3O2)', '(CH3COO)');
	$halide_exceptions = array('Ag' => 1, 'Pb' => 2, 'Hg2' => 2, 'Cu' => 1); //Format is atom => charge
	$sulfate_exceptions = array('Ba', 'Sr', 'Ca', 'Pb', 'Hg2', 'Ag'); 
	$hydroxide_exceptions = array('Ba', 'Ca', 'Sr');
	
	//Add all alkali metals to the $soluble array
	foreach(array_keys(getTable(null, null, 'group1')) as $alkali){
		if($alkali != 'H'){
			$soluble[] = $alkali;
		}
	}
	//If any of the always soluble atoms are in the molecule, return true
	foreach($soluble as $atom){
		
		//If a soluble atom is in the molecule
		if(strpos($molecule, $atom) !== false){
			
			//Add information to the work array and return true
			if($showWork){
				$_SESSION['work'][] = $molecule .  " is soluble: ammonium, nitrate, chlorate, perchlorate, and acetate salts are always soluble.";
			}
			return true;
		}
	}
	
	//Check for halides
	foreach(array_keys(getTable(null, null, 'group17')) as $halide){
		
		//Check if a halide exists in the molecule
		if(strpos($molecule, $halide) !== false){
			
			//If there is a halide, check for exceptions
			foreach(array_keys($halide_exceptions) as $exception){
				
				//If an exception exists in the molecule, check to make sure the charge is correct for exceptions
				if(strpos($molecule, $exception) !== false){
					
					//Check first if the exception has a static charge
					if(is_int(getTable($exception, 'charge'))){
			
						//If it has a static charge, check if the charge is soluble
						if($halide_exceptions[$exception] != getTable($exception, 'charge')){

							//Add information to the work array and return true
							if($showWork){
								$_SESSION['work'][] = $molecule .  " is soluble: halide salts are soluble with the exception of Ag<sup>+</sup>, Pb<sup>2+</sup>, Hg<sub>2</sub><sup>2+</sup>";
							}
							return true;
						}else{
							//Add information to the work array and return false
							if($showWork){
							$_SESSION['work'][] = $molecule .  " is insoluble: halide salts are soluble with the exception of Ag<sup>+</sup>, Pb<sup>2+</sup>, Hg<sub>2</sub><sup>2+</sup>";
							}
							return false;
						}
					}else{
		
						//Check the charge of the exception vs. the charge of the element found by accession the session variable
						if($halide_exceptions[$exception] != $_SESSION['transitions'][$exception]){
							//Add information to the work array and return true
							if($showWork){
							$_SESSION['work'][] = $molecule .  " is soluble: halide salts are soluble with the exception of Ag<sup>+</sup>, Pb<sup>2+</sup>, Hg<sub>2</sub><sup>2+</sup>";
							}
							return true;
						}else{
							//Add information to the work array and return false
							if($showWork){
							$_SESSION['work'][] = $molecule .  " is insoluble: halide salts are soluble with the exception of Ag<sup>+</sup>, Pb<sup>2+</sup>, Hg<sub>2</sub><sup>2+</sup>";
							}
							return false;
						}
					}
				}
			}
		//Add information to the work array and return true
		if($showWork){
		$_SESSION['work'][] = $molecule .  " is soluble: halide salts are soluble with the exception of Ag<sup>+</sup>, Pb<sup>2+</sup>, Hg<sub>2</sub><sup>2+</sup>";
		}
		return true;	
		}
	}
	
	//Check for hydroxides
	if(strpos($molecule, 'OH') !== false){
		
		//If there is a hydroxide insoluble exception, return true
		foreach($hydroxide_exceptions as $exception){
			if(strpos($molecule, $exception) !== false){
				
				//Add information to the work array and return true
				if($showWork){
				$_SESSION['work'][] = $molecule .  " is soluble: hydroxide salts are soluble with the exception of Ba<sup>2+</sup>, Ca<sup>2+</sup>, Sr<sub>2+";
				}
				return true;
			}
		}
		
		return false;
	}
	
	
	//Check for sulfates
	if(strpos($molecule, 'SO4') !== false){
		
		//If there is a hydroxide insoluble exception, return true
		foreach($sulfate_exceptions as $exception){
			if(strpos($molecule, $exception) !== false){
				
				//Add information to the work array and return true
				if($showWork){
				$_SESSION['work'][] = $molecule .  " is insoluble: sulfate salts are soluble with the exception of Ba<sup>2+</sup>, Ca<sup>2+</sup>, Sr<sub>2+";
				}
				return false;
			}
		}
		
		return true;
	}
	//If nothing proved it soluble, assume it is insoluble.
	return false;
}

//Takes input reactants and products, both strings. Outputs a complete string that is the balanced equation, using --> as a yields symbol
function balanceEquation($reactants, $products){
	
	//Split to molecules and ensure that the it is the correct form AaBb CcDd --> AeDf + CgBh
	$first = $reactants;
	$second = $products;
	
	
	$first = splitEquation($first, 2);
	$second = splitEquation($second, 2); //This area is poorly written. If you see a more effective strategy feel free to implement it.
	
	if(splitEquation($first[0], 4)[0] !== splitEquation($second[0], 4)[0]){
		$first = array_reverse($first);
	}
	
	//Glue the pieces back together
	$reactants = implode(' + ', $first);
	
	//Remove any coefficients and split to atoms
	$reactants = array_values(array_unique(splitEquation($reactants, 3)));
	$products = array_values(array_unique(splitEquation($products, 3)));
	
	//takes the coefficients from the match charges function to assign trailing values
	$a = matchCharges($reactants[0], $reactants[1], true)['cation'];
	$b = matchCharges($reactants[0], $reactants[1], true)['anion'];
	$c = matchCharges($reactants[2], $reactants[3], true)['cation'];
	$d = matchCharges($reactants[2], $reactants[3], true)['anion'];
	
	$e = matchCharges($products[0], $products[1], true)['cation'];
	$f = matchCharges($products[0], $products[1], true)['anion'];
	$g = matchCharges($products[2], $products[3], true)['cation'];
	$h = matchCharges($products[2], $products[3], true)['anion'];
	
	//Assign values to trailing numbers using ternary operator -- DEPRECATED (such a cody and legit word)
	/*$a = (is_numeric(substr(trim($reactants[0]), -1, 1)) ? $a = substr(trim($reactants[0]), -1, 1) : $a = 1);
	$b = (is_numeric(substr(trim($reactants[1]), -1, 1)) ? $b = substr(trim($reactants[1]), -1, 1) : $b = 1);
	$c = (is_numeric(substr(trim($reactants[2]), -1, 1)) ? $c = substr(trim($reactants[2]), -1, 1) : $c = 1);
	$d = (is_numeric(substr(trim($reactants[3]), -1, 1)) ? $d = substr(trim($reactants[3]), -1, 1) : $d = 1);
	
	$e = (is_numeric(substr(trim($products[0]), -1, 1)) ? $e = substr(trim($products[0]), -1, 1) : $e = 1);
	$f = (is_numeric(substr(trim($products[1]), -1, 1)) ? $f = substr(trim($products[1]), -1, 1) : $f = 1);
	$g = (is_numeric(substr(trim($products[2]), -1, 1)) ? $g = substr(trim($products[2]), -1, 1) : $g = 1);
	$h = (is_numeric(substr(trim($products[3]), -1, 1)) ? $h = substr(trim($products[3]), -1, 1) : $h = 1);*/
	
	$w = 12;
	$y = ($w * $a)/$e;
	$z = ($w * $b)/$h;
	$x = ($w * $a * $f)/($e * $d);
	$nums = array($w, $x, $y, $z);
	
	//If possible find greatest common factor of the coefficients
	$lcd = array_reduce(array($w, $x, $y, $z), 'gcf');
	if(is_int($lcd)){
		
		//Simplify coefficients
		foreach($nums as &$num){
			$num = $num/$lcd;
		}
	}
	
	$_SESSION['work'][] = "To balance the equation, requires a ratio of $nums[0]:$nums[1] --> $nums[2]:$nums[3] for the atoms.";
	
	//If coefficient is one, remove it
	foreach($nums as &$num){
		if($num == 1){
			$num = '';
		}
	}
	
	//Return formatted equation with state symbols
	return formatEquation("$nums[0]$reactants[0]$reactants[1] + $nums[1]$reactants[2]$reactants[3]") .  " --> " . formatEquation("$nums[2]$products[0]$products[1] + $nums[3]$products[2]$products[3]");
	
}

//Returns formatted equation
function formatEquation($equation){
	
	
	//Remove parenthesis if it has a single polyatomic
	foreach(array_keys(getTable(null, null, 'polyatomics')) as $poly){
	
		//If a polyatomic exists
		if(strpos($equation, $poly) !== false){
			
			if( (!isset($equation{strpos($equation, $poly) + strlen($poly)})) || (!is_numeric($equation{strpos($equation, $poly) + strlen($poly)}))){
				$changed = strtr($poly, array('(' => '', ')' => ''));	
				$equation = str_replace($poly, $changed, $equation);
			}
			
		}
	}	
	
	//Value for formatted equation
	$formatted = $equation;
	
	//Explode equation into individual molecules
	$molecules = array_unique(splitEquation($equation, 2));
	
	//Replace all numbers as subscripts
	
	foreach($molecules as &$molecule){
	
		//Array to hold all single digit numbers
		$numbers = array('1', '2', '3', '4', '5', '6', '7', '8', '9');
		
		$original = $molecule;
		foreach($numbers as $number){
			
			
			$molecule = str_replace($number, "<sub class = 'small'>$number</sub>", $molecule);
			
			//Add state symbols
			if(isSoluble($molecule)){
				if( (strpos($molecule, "<sub class = 'small'>(s)</sub>") === false) && (strpos($molecule, "<sub class = 'small'>(aq)</sub>") === false) ){
					$molecule .= "<sub class = 'small'>(aq)</sub>";
				}
			}else{
				if( (strpos($molecule, "<sub class = 'small'>(s)</sub>") === false) && (strpos($molecule, "<sub class = 'small'>(aq)</sub>") === false) ){
					$molecule .= "<sub class = 'small'>(s)</sub>";
				}
			}
			
			
		}
		
		$formatted = str_replace($original, $molecule, $formatted);
	}
	
	return $formatted;
}

//Converts from roman numerals to arabic numbering
function toArabic($number){
	switch($number){
		
		case 'I':	
			return 1;
			break;
		case 'II':	
			return 2;
			break;
		case 'III':	
			return 3;
			break;	
		case 'IV':	
			return 4;
			break;	
		case 'V':	
			return 5;
			break;
		case 'VI':	
			return 6;
			break;		
	}
	return -1;
}

//Function that returns the greatest common factor of two values
function gcf($a, $b){
	return ( $b == 0 ) ? ($a):( gcf($b, $a % $b) ); 
}

//Return the least common multiple of two numbers
function lcm($a, $b) { 
	return ( $a / gcf($a, $b) ) * $b; 
}

//Return the least common multiple of more than two numbers
function lcmNums($ar) {
	if (count($ar) > 1) {
		$ar[] = lcm( array_shift($ar) , array_shift($ar) );
		return lcm_nums( $ar );
	} else {
		return $ar[0];
	}
}
?>