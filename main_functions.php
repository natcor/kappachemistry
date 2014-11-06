<?php

function getPrecipitation($reactants){
	
	//Convert words
	//$reactants = convertWords($reactants);
	
	//Check if the reaction is valid for a precipitation (i.e. if the charges of the atoms match)
	if(!isValid($reactants)){
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
			return "Multiple Precipitation Reactions Possible.";
		}
	}
	
}

//Checks where an acid base reaction is possible
function getAcidBase($reactants){
	
	
	if(!isValid($reactants)){
		return false;
	}
	
	$strongReaction = checkStrongReaction(splitEquation($reactants, 2));

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
	
	if($strongReaction){
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
	$atoms = array_values(array_unique(splitEquation($reactants)));
	
	//Check to ensure all are elements
	foreach($atoms as $atom){
		
		//Check to ensure not a polyatomic
		$polyatomics = array_keys(getTable(null, null, 'polyatomics'));
		if(in_array($atom, $polyatomics)){
			return false;
		}
	}
	
	//Ensure correct cation/anion order
	if(getTable($atoms[0], 'electronegativity') > getTable($atoms[1], 'electronegativity')){
		$atoms = array_reverse($atoms);
	}
	
	$_SESSION['work'][] = "Basic combination reaction.";
	
	//Find the product
	$product = matchCharges($atoms[0], $atoms[1]);
	
	//Check for hindenburg
	if( (in_array('H', $atoms)) && (in_array('O', $atoms))){
		$product = '<img src = "hindenburg.png" class = "blimp">';
		$_SESSION['work'][] = "Never take chemistry from a German. Especially one alive during World War II. For more than one reason.";
		return formatEquation($reactants) . ' --> ' . $product;
	}
	
	return balanceEquation($reactants, $product);
	
}

?>