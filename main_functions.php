<?php

function getPrecipitation($reactants){
	
	//Check if the reaction is valid for a precipitation (i.e. if the charges of the atoms match)
	if(!isValid($reactants)){
		return false;
	}
	
	foreach(splitEquation($reactants, 2) as $molecule){
		
		if(!isSoluble($molecule)){
			$_SESSION['work'][] = "The reactant $molecule is not soluble, so no reaction will take place.";
			return "$reactants --> No Precipitation Reaction";
		}
	}
	
	//Check that neither of the reactants is insoluble
	$compounds = splitEquation($reactants, 2);
	if(!isSoluble($compounds[0]) || !isSoluble($compounds[1])){
		echo 'One of these is already a solid, silly.  Good luck trying to mix it.';
		return $reactants . " --> " . $reactants;
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
			if(!isSoluble(matchCharges($cation, $anion)) ){ //If the combination is soluble
				
				//Remove the precipitate ions from the reactants array
				unset($cations[array_search($cation, $cations)]);
				unset($anions[array_search($anion, $anions)]);
				
				//Add the combination to the precipitate array in the correct ratios
				$precipitates[] = matchCharges($cation, $anion);
			}
		}
	}
	
<<<<<<< HEAD
	//If only one possible precipitate formed
	if(count($precipitates) === 1){
=======
	//BaCl2 + K2SO4 doesn't work
	
	//If only one possible precipitate formed  // is this redundant now?
	if(count($precipitates == 1)){
>>>>>>> FETCH_HEAD
		
		//Balance the reaction
		$balancedEquation = balanceEquation($reactants, $precipitates[0] . ' + ' . matchCharges(array_shift($cations), array_shift($anions)) );
		return $balancedEquation;
	}else{
		if(count($precipitates) == 0){
			return "$reactants --> No Precipitation Reaction";
		}
		if(count($precipitates) > 1){
			return "Multiple Precipitation Reactions Possible.";
		}
	}
}

?>