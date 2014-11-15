<?php

function getPrecipitation($reactants){
	
	//Convert words
	//$reactants = convertWords($reactants);
	
	//Check if the reaction is valid for a precipitation (i.e. if the charges of the atoms match)
	if(!isValid($reactants)){
		return false;
	}
	
	//Make sure the compound is ionic
	$checkAtoms = array_values(array_unique(splitEquation($reactants)));
	
	if( abs(getTable($checkAtoms[0], 'electronegativity') - getTable($checkAtoms[1], 'electronegativity') ) < 1){
		return false;
	}
	
	
	foreach(splitEquation($reactants, 2) as $molecule){
		
		if(!isSoluble($molecule)){
			isSoluble($molecule, true);
			$_SESSION['work'][] = "$molecule is already a solid, silly.  Good luck trying to mix it.";
			return false;
		}
		
		
	}
	
	//Split reactants into individual atoms
	$atoms = splitEquation($reactants);
	
	//Create an array to store cations and anions
	$cations = $anions = array();
	
	//Sort atoms into cations or anions
	foreach($atoms as $atom){
		
		//If the element is an anion, place it in the appropriate array
		if(getTable($atom, 'electronegativity') >= 2.0 ){
			$anions[] = $atom;
		}else{ //If not an anion, must be a cation
			$cations[] = $atom;
		}
	}
	
	$precipitates = array();
	
	//Check every possible anion/cation possiblility for solubility
	foreach($cations as $cation){
		foreach($anions as $anion){
			
			if(!isSoluble(matchCharges($cation, $anion), true) ){ //If the combination is soluble
				//Remove the precipitate ions from the reactants array
				unset($cations[array_search($cation, $cations)]);
				unset($anions[array_search($anion, $anions)]);
				
				//Add the combination to the precipitate array in the correct ratios
				$precipitates[] = matchCharges($cation, $anion);
			}
		}
	}
	
	if(count($precipitates) < 1){
		return false;
	}
	
	if(count($precipitates == 1)){
		
		//Balance the reaction
		$balancedEquation = balanceEquation($reactants, $precipitates[0] . ' + ' . matchCharges(array_shift($cations), array_shift($anions)) );
		return $balancedEquation;
	}else{
		
		if(count($precipitates) > 1){
			return false;
		}
	}
	
}

//Checks where an acid base reaction is possible
function getAcidBase($reactants){
	
	
	if(!isValid($reactants)){
		return false;
	}
	
	//Make sure that there is at least two molecules	
	if(count(splitEquation($reactants, 2)) < 2){
		return false;
	}
	$acidBase = checkAcidBase(splitEquation($reactants, 2));
	
	//Split equation into molecules
	$ions = splitEquation($reactants);
	
	//Array to hold products
	$products = array();

	//Test for water formation and add water if necessary
	if( (in_array('(OH)', $ions)) && (in_array('H', $ions)) ){
		$products[] = 'HOH';
		foreach($ions as &$ion){
			if(($ion == 'H') || ($ion == '(OH)')){
				$ion = '';
			}
		}
		$ions = array_values(array_filter($ions));
	}else{
		//If not hydrogen and hydroxide check for week base /acids
		if( (isset($acidBase[0])) && (isset($acidBase[1] )) ){
			
			$product = $acidBase[0][1] . " + " . $acidBase[1][0];
			$_SESSION['work'][] = $acidBase[1][0] . " accepts the proton(s), " . $acidBase[0][1] . " is the donor.";
			$_SESSION['work'][] = $acidBase[1][0] . " is the conjugate acid of the original base. " . $acidBase[0][1] . " is a conjugate base of the original acid.";		
			$_SESSION['work'][] = 'Note: Some strong and weak acids do not completely disassociate. For example, H3PO4 in aqueous solution can dissolve into H2PO4 or HPO4, both of which are <a class = "inline" href = http://chemistry.about.com/od/chemistryglossary/g/Amphiprotic-Definition.htm target = "_blank">amphiprotic</a>, or PO4 (a weak base).';
			return formatEquation($reactants) . ' --> ' . formatEquation($product);
			
		}
		
	}
	
	//Ensure correct cation/anion order
	if(getTable($ions[0], 'electronegativity') > getTable($ions[1], 'electronegativity')){
		$ions = array_reverse($ions);
	}
	
	//Add other molecule
	if(count($ions) == 2){
		$products[] = matchCharges($ions[0], $ions[1]);
		
	}else{
		return false;
	}
	
	//Check if it is a strong reaction or not
	if($acidBase[2]){
		$_SESSION['work'][] = "Water will be formed since it is a neutralization reaction of a strong acid and strong base.";
		$symbol = ' --> ';
	}else{
		$symbol = ' <--> ';
	}
	$products = implode(' + ',$products);
	
	return balanceEquation($reactants, $products);
}

function getSynthesis($reactants){
	
	//Split into atoms 
	$atoms = array_values(array_unique(splitEquation($reactants, 3)));
	$molecules = array_values(array_unique(splitEquation($reactants, 2)));
	
	if($atoms !== $molecules){
		
		//Send equation to variable
		$_SESSION['failedEquation'] = $reactants;
		return false;
		
	}
	
	if(count(explode('+', $reactants)) < 2){
		return false;
	}
	
	//Check to ensure all are elements
	foreach($atoms as $atom){
		
		//Check to ensure not a polyatomic
		$polyatomics = array_keys(getTable(null, null, 'polyatomics'));
		if(in_array($atom, $polyatomics)){
			return false;
		}
	}
	
	$splitAtoms = array_values(array_unique(splitEquation($reactants)));
	
	//Ensure correct cation/anion order
	if(getTable($splitAtoms[0], 'electronegativity') > getTable($splitAtoms[1], 'electronegativity')){
		echo getTable($atoms[0], 'electronegativity');
		
		$atoms = array_reverse($atoms);
		
	}
	
	$_SESSION['work'][] = "Basic synthesis reaction. All of the reactants are in their elemental state, so the equation takes the form A + B --> C";
	
	//Find the product
	$product = matchCharges($atoms[0], $atoms[1]);
	
	return balanceEquation(splitEquation($reactants, 2), $product);
	
}

function getCombustion($reactants){
	$molecules = splitEquation($reactants, 2);
	$hasHydrocarbon = false;
	$hasOxygen = false;
	foreach($molecules as $molecule){
		if( (strpos($molecule, 'H') !== false) && (strpos($molecule, 'C') !== false)){
			$hasHydrocarbon = true;
			continue;
		}
		if(strpos($molecule, 'O2') !== false){
			$hasOxygen = true;
		}
	}
	
	//Make sure there is a hydrocarbon and oxygen
	if(!$hasHydrocarbon || !$hasOxygen){
		return false;
	}
	
	$_SESSION['work'][] = "It is a combustion reaction! The products of a combustion reaction are always water and carbon dioxide";
	$_SESSION['work'][] = "Note: this reaction would never occur completely as written. Incomplete combustion will also take place, resulting in products such as CO (carbon monoxide) or C (charcoal)";
	
	$products = 'H2O + CO2';
	return balanceEquation($reactants, $products);
}

?>