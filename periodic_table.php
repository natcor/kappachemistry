<?php

//Table that includes information about all the (common) elements

//Function takes as parameters an element and a property desired. Returns the property value.
function getTable($element, $property, $array = null){
	//Create array to hold periodic table
	$pTable = array(
		//Create an array for each group
		"group1" => array(
			//Data in each elements array are as follows: atomic number, molar mass, electronegativiy
			"H" => array(
				"atomic_number" => 1,
				"molar_mass" => 1.008, 
				"electronegativity" => 2.1,
				"charge" => 1,
				"reactivity" => 16
			),
			"Li" => array(
				"atomic_number" => 3,
				"molar_mass" => 6.94, 
				"electronegativity" => 1.0,
				"charge" => 1,
				"reactivity" => 1
			),
			"Na" => array(
				"atomic_number" => 11,
				"molar_mass" => 22.989, 
				"electronegativity" => 0.9,
				"charge" => 1,
				"reactivity" => 5
			),
			"K" => array(
				"atomic_number" => 19,
				"molar_mass" => 39.0983, 
				"electronegativity" => 0.8,
				"charge" => 1,
				"reactivity" => 2
			),
			"Rb" => array(
				"atomic_number" => 37,
				"molar_mass" => 85.4678, 
				"electronegativity" => 0.8,
				"charge" => 1
			),
			"Cs" => array(
				"atomic_number" => 55,
				"molar_mass" => 85.4678, 
				"electronegativity" => 0.7,
				"charge" => 1
			),
			"Fr" => array(
				"atomic_number" => 87,
				"molar_mass" => 132.90, 
				"electronegativity" => 0.8,
				"charge" => 1
			)
		),
		"group2" => array(
			//Data in each elements array are as follows: atomic number, molar mass, electronegativiy
			"Be" => array(
				"atomic_number" => 4,
				"molar_mass" => 9.0121, 
				"electronegativity" => 1.5,
				"charge" => 2
					
			),
			"Mg" => array(
				"atomic_number" => 12,
				"molar_mass" => 24.305, 
				"electronegativity" => 1.2,
				"charge" => 2,
				"reactivity" => 6
			),
			"Ca" => array(
				"atomic_number" => 20,
				"molar_mass" => 40.078, 
				"electronegativity" => 1.0,
				"charge" => 2,
				"reactivity" => 4
			),
			"Sr" => array(
				"atomic_number" => 38,
				"molar_mass" => 87.62, 
				"electronegativity" => 1.0,
				"charge" => 2
			),
			"Ba" => array(
				"atomic_number" => 56,
				"molar_mass" => 137.327, 
				"electronegativity" => 0.9,
				"charge" => 2,
				"reactivity" => 3
			),
			"Ra" => array(
				"atomic_number" => 88,
				"molar_mass" => 226, 
				"electronegativity" => 0.9,
				"charge" => 2
			)
		),
		"group16" => array(
			"O" => array(
				"atomic_number" => 8,
				"molar_mass" => 15.999, 
				"electronegativity" => 3.5,
				"charge" => -2
			),
			"S" => array(
				"atomic_number" => 16,
				"molar_mass" => 32.06, 
				"electronegativity" => 2.5,
				"charge" => -2
			),
			"Se" => array(
				"atomic_number" => 34,
				"molar_mass" => 78.971, 
				"electronegativity" => 2.4,
				"charge" => -2
			),
			"Te" => array(
				"atomic_number" => 52,
				"molar_mass" => 127.60, 
				"electronegativity" => 2.1,
				"charge" => -2
			),
			"Po" => array(
				"atomic_number" => 84,
				"molar_mass" => 209, 
				"electronegativity" => 2.0,
				"charge" => -2
			),
			"Lv" => array(
				"atomic_number" => 116,
				"molar_mass" => 293, 
				"electronegativity" => null,
				"charge" => -2
			)
		),
		"group17" => array(
			"F" => array(
				"atomic_number" => 9,
				"molar_mass" => 18.998, 
				"electronegativity" => 4.0,
				"charge" => -1
			),
			"Cl" => array(
				"atomic_number" => 17,
				"molar_mass" => 35.45, 
				"electronegativity" => 3.0,
				"charge" => -1
			),
			"Br" => array(
				"atomic_number" => 35,
				"molar_mass" => 79.904, 
				"electronegativity" => 2.8,
				"charge" => -1
			),
			"I" => array(
				"atomic_number" => 53,
				"molar_mass" => 126.90, 
				"electronegativity" => 2.5,
				"charge" => -1
			),
			"At" => array(
				"atomic_number" => 85,
				"molar_mass" => 210, 
				"electronegativity" => 2.2,
				"charge" => -1
			),
			"Uus" => array(
				"atomic_number" => 117,
				"molar_mass" => 294, 
				"electronegativity" => null,
				"charge" => -1
			)
		),
		"transition_metals" => array(
			
			//Ones that can only take one charge
			"Zn" => array(
				"name" => 'zinc',
				"atomic_number" => 30,
				"molar_mass" => 65.38, 
				"electronegativity" => 1.6,
				"charge" => 2,
				"reactivity" => 9
			),
			"Hg" => array(
				"name" => 'mercury',
				"atomic_number" => 80,
				"molar_mass" => 200.59, 
				"electronegativity" => 1.9,
				"charge" => 2,
				"reactivity" => 19
			),
			"Ag" => array(
				"name" => 'silver',
				"atomic_number" => 47,
				"molar_mass" => 107.8682, 
				"electronegativity" => 1.9,
				"charge" => 1,
				"reactivity" => 18
			),
			"Al" => array(
				"name" => 'aluminum',
				"atomic_number" => 13,
				"molar_mass" => 26.981, 
				"electronegativity" => 1.5,
				"charge" => 3,
				"reactivity" => 7
			),
			
			//Ones that can take multiple charges have arrays for their ion form
			"Fe" => array(
				"name" => 'iron',
				"atomic_number" => 26,
				"molar_mass" => 55.845,
				"electronegativity" => 1.8,
				"charge" => array(2, 3),
				"reactivity" => 11
					
			),
			"Pt" => array(
				"name" => 'platinum',
				"atomic_number" => 78,
				"molar_mass" => 195.084,
				"electronegativity" => 1.9, //Actual 2.2 but don't want to say that because throws off values
				"charge" => array(2, 3),
				"reactivity" => 20
					
			),
			"Au" => array(
				"name" => 'gold',
				"atomic_number" => 79,
				"molar_mass" => 196.96,
				"electronegativity" => 1.9, //Actual 2.4 but don't want to say that because throws off values
				"charge" => array(1, 3),
				"reactivity" => 21
					
			),
			"Cu" => array(
				"name" => 'copper',
				"atomic_number" => 29,
				"molar_mass" => 63.546,
				"electronegativity" => 1.9,
				"charge" => array(1, 2),
				"reactivity" => 17
			),
			"Co" => array(
				"name" => 'cobalt',
				"atomic_number" => 27,
				"molar_mass" => 58.933,
				"electronegativity" => 1.8,
				"charge" => array(2, 3),
				"reactivity" => 12
			),
			"Sn" => array(
				"name" => 'tin',
				"atomic_number" => 50,
				"molar_mass" => 118.710,
				"electronegativity" => 1.8,
				"charge" => array(2, 4),
				"reactivity" => 14
			),
			"Pb" => array(
				"name" => 'lead',
				"atomic_number" => 82,
				"molar_mass" => 207.2	,
				"electronegativity" => 1.9,
				"charge" => array(2, 4),
				"reactivity" => 15
			),
			"Ni" => array(
				"name" => 'nickel',
				"atomic_number" => 28,
				"molar_mass" => 58.6934	,
				"electronegativity" => 1.9,
				"charge" => array(2, 4), 
				"reactivity" => 13
			),
			"Mn" => array(
				"name" => 'manganese',
				"atomic_number" => 25,
				"molar_mass" => 54.938	,
				"electronegativity" => 1.5,
				"charge" => array(2, 3, 4, 6),
				"reactivity" => 8
			),
			"Cr" => array(
				"name" => 'chromium',
				"atomic_number" => 24,
				"molar_mass" => 51.9961	,
				"electronegativity" => 1.6,
				"charge" => array(2, 3),
				"reactivity" => 10
			),
			"Ti" => array(
				"name" => 'titanium',
				"atomic_number" => 22,
				"molar_mass" => 47.867	,
				"electronegativity" => 1.5,
				"charge" => array(2, 3, 4),
				"reactivity" => 10
			)
			
		),
		
		"polyatomics" => array(
			
			//DISLCLAIMER: polyatomic ions do not have electronegativies since they are molecules. The electronegativly given is simply to determine whether the molecule is utilized generally as a cation or anion in ionic bonding.
			
			"(OH)" => array(
				"name" => 'hydroxide',
				"molar_mass" => 18.998, 
				"electronegativity" => 3,
				"charge" => -1
			),
			"(NO3)" => array(
				"name" => 'nitrate',
				"molar_mass" => 35.45, 
				"electronegativity" => 3,
				"charge" => -1
			),
			"(PO4)" => array(
				"name" => 'phosphate',
				"molar_mass" => 94.97, 
				"electronegativity" => 3,
				"charge" => -3
			),
			"(NH4)" => array(
				"name" => 'ammonium',
				"molar_mass" => 18.04, 
				"electronegativity" => 1,
				"charge" => 1
			),
			"(NH3)" => array(
				"name" => 'ammonia',
				"molar_mass" => 17.03, 
				"electronegativity" => 1,
				"charge" => 0
			),
			"(SO4)" => array(
				"name" => 'sulfate',
				"molar_mass" => 96.06, 
				"electronegativity" => 3,
				"charge" => -2
			),
			"(CO3)" => array(
				"name" => 'carbonate',
				"molar_mass" => 60.01, 
				"electronegativity" => 3,
				"charge" => -2
			),
			"(CrO4)" => array(
				"name" => 'chromate',
				"molar_mass" => 115.99, 
				"electronegativity" => 3,
				"charge" => -2
			),
			"(C2H3O2)" => array(
				"name" => 'acetate',
				"molar_mass" => 59.04, 
				"electronegativity" => 3,
				"charge" => -1
			),
			"(CH3COO)" => array(
				"name" => 'acetate',
				"molar_mass" => 59.04, 
				"electronegativity" => 3,
				"charge" => -1
			)
		)
	);
	
	if($array){
		return $pTable[$array];
	}
	//Loop through groups until the desired element is found, then return the requested property of it
	foreach($pTable as $group){
		if(isset($group[$element])){
			return $group[$element][$property];
		}
	}
	return -1;
}

?>