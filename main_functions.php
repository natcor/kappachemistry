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
function getAcidBase($equation){
	
	//Split equation into molecules
	$molecules = splitEquation($equation, 2);
	
	//Create array to hold soluble atoms
	$ions = array();
	
	//Add all soluble atoms into the ions array, and remove parenthesis around polyatomics to make things like OH more accessible
	foreach($molecules as $molecule){
		if(isSoluble($molecule)){
			$atoms = splitEquation($molecule);
			foreach($atoms as $atom){
				$ions[] = strtr($atom, array('(OH)' => 'OH'));
			}
		}
	}
	
	//Create array to hold the products
	$products = '';
	
	if( (in_array('H', $ions)) && (in_array('OH', $ions)) ){
		$products .= 'H<sub class = "small">2</sub>O<sub class = "small">(l)</sub> + ';
		foreach($ions as &$ion){
			if( ($ion == 'H') || ($ion == 'OH') ){
				$ion = '';
			}
		}
	}
	
	//Filter ions array
	$ions = array_values(array_filter($ions));
	
	//Add other molecule
	if(count($ions) == 2){
		$products .= formatEquation(matchCharges($ions[0], $ions[1]));
		
	}else{
		return false;
	}
	
	return formatEquation($equation) . ' --> ' . $products;
}

?>