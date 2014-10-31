<?php

//Table that includes information about all the elements
function getTable($element, $property){
	//Create array to hold periodic table
	$table = array(
		//Create an array for each group
		"group1" => array(
			//Data in each elements array are as follows: atomic number, molar mass, electronegativiy
			"H" => array(
				"atomic_number" => 1,
				"molar_mass" => 1.008, 
				"electronegativity" => 2.1,
				"ion" => 1
			),
			"Li" => array(
				"atomic_number" => 3,
				"molar_mass" => 6.94, 
				"electronegativity" => 1.0,
				"ion" => 1	
			),
			"Na" => array(
				"atomic_number" => 11,
				"molar_mass" => 22.989, 
				"electronegativity" => 0.9,
				"ion" => 1
			),
			"K" => array(
				"atomic_number" => 19,
				"molar_mass" => 39.0983, 
				"electronegativity" => 0.8,
				"ion" => 1
			),
			"Rb" => array(
				"atomic_number" => 37,
				"molar_mass" => 85.4678, 
				"electronegativity" => 0.8,
				"ion" => 1
			),
			"Cs" => array(
				"atomic_number" => 55,
				"molar_mass" => 85.4678, 
				"electronegativity" => 0.7,
				"ion" => 1
			),
			"Fr" => array(
				"atomic_number" => 87,
				"molar_mass" => 132.90, 
				"electronegativity" => 0.8,
				"ion" => 1
			)
		),
		"group2" => array(
			//Data in each elements array are as follows: atomic number, molar mass, electronegativiy
			"Be" => array(
				"atomic_number" => 4,
				"molar_mass" => 9.0121, 
				"electronegativity" => 1.5,
				"ion" => 2
					
			),
			"Mg" => array(
				"atomic_number" => 12,
				"molar_mass" => 24.305, 
				"electronegativity" => 1.2,
				"ion" => 2
			),
			"Ca" => array(
				"atomic_number" => 20,
				"molar_mass" => 40.078, 
				"electronegativity" => 1.0,
				"ion" => 2
			),
			"Sr" => array(
				"atomic_number" => 38,
				"molar_mass" => 87.62, 
				"electronegativity" => 1.0,
				"ion" => 2
			),
			"Ba" => array(
				"atomic_number" => 56,
				"molar_mass" => 137.327, 
				"electronegativity" => 0.9,
				"ion" => 2
			),
			"Ra" => array(
				"atomic_number" => 88,
				"molar_mass" => 226, 
				"electronegativity" => 0.9,
				"ion" => 2
			)
		),
		"group16" => array(
			"O" => array(
				"atomic_number" => 8,
				"molar_mass" => 15.999, 
				"electronegativity" => 3.5,
				"ion" => -2
			),
			"S" => array(
				"atomic_number" => 16,
				"molar_mass" => 32.06, 
				"electronegativity" => 2.5,
				"ion" => -2
			),
			"Se" => array(
				"atomic_number" => 34,
				"molar_mass" => 78.971, 
				"electronegativity" => 2.4,
				"ion" => -2
			),
			"Te" => array(
				"atomic_number" => 52,
				"molar_mass" => 127.60, 
				"electronegativity" => 2.1,
				"ion" => -2
			),
			"Po" => array(
				"atomic_number" => 84,
				"molar_mass" => 209, 
				"electronegativity" => 2.0,
				"ion" => -2
			),
			"Lv" => array(
				"atomic_number" => 116,
				"molar_mass" => 293, 
				"electronegativity" => null,
				"ion" => -2
			)
		),
		"group17" => array(
			"F" => array(
				"atomic_number" => 9,
				"molar_mass" => 18.998, 
				"electronegativity" => 4.0,
				"ion" => -1
			),
			"Cl" => array(
				"atomic_number" => 17,
				"molar_mass" => 35.45, 
				"electronegativity" => 3.0,
				"ion" => -1
			),
			"Br" => array(
				"atomic_number" => 35,
				"molar_mass" => 79.904, 
				"electronegativity" => 2.8,
				"ion" => -1
			),
			"I" => array(
				"atomic_number" => 53,
				"molar_mass" => 126.90, 
				"electronegativity" => 2.5,
				"ion" => -1
			),
			"At" => array(
				"atomic_number" => 85,
				"molar_mass" => 210, 
				"electronegativity" => 2.2,
				"ion" => -1
			),
			"Uus" => array(
				"atomic_number" => 117,
				"molar_mass" => 294, 
				"electronegativity" => null,
				"ion" => -1
			)
		),
		"transition_metals" => array(
			"Zn" => array(
				"atomic_number" => 30,
				"molar_mass" => 65.38, 
				"electronegativity" => 1.6,
				"ion" => 2
			),
			"Ag" => array(
				"atomic_number" => 47,
				"molar_mass" => 107.8682, 
				"electronegativity" => 1.9,
				"ion" => 1
			),
			"Al" => array(
				"atomic_number" => 13,
				"molar_mass" => 26.981, 
				"electronegativity" => 1.5,
				"ion" => 3
			),
			
		),
		"polyatmomics" => array(
			"OH" => array(
				"atomic_number" => 9,
				"molar_mass" => 18.998, 
				"electronegativity" => 2.8,
				"ion" => -1
			),
			"NO3" => array(
				"atomic_number" => 17,
				"molar_mass" => 35.45, 
				"electronegativity" => 2.8,
				"ion" => -1
			),
			"PO4" => array(
				"atomic_number" => 35,
				"molar_mass" => 79.904, 
				"electronegativity" => 2.8,
				"ion" => -3
			),
			"NH4" => array(
				"atomic_number" => 35,
				"molar_mass" => 79.904, 
				"electronegativity" => 1,
				"ion" => 1
			),
			"SO4" => array(
				"atomic_number" => 9,
				"molar_mass" => 18.998, 
				"electronegativity" => 2.8,
				"ion" => -2
			),
		)
	);
	
	//Loop through groups until the desired element is found, then return the requested property of it
	foreach($table as $group){
		if(isset($group[$element])){
			return $group[$element][$property];
		}
	}
	return -1;
}

?>