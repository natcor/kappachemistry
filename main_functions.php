<?php

function getPrecipitation($reactants){
	
	//Convert words
	$reactants = convertWords($reactants);
	
	//Check if the reaction is valid for a precipitation (i.e. if the charges of the atoms match)
	if(!isValid($reactants)){
		return false;
	}
	
	foreach(splitEquation($reactants, 2) as $molecule){
		if(!isSoluble($molecule)){
			isSoluble($molecule, true);
			$_SESSION['work'][] = "$molecule is already a solid, silly.  Good luck trying to mix it.";
			return "$reactants --> No Precipitation Reaction";
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
		return "$reactants --> No Precipitation Reaction";
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

?>