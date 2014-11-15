<?php


//Equation balancer 2.0
//Uses matrices to balance any equations
//Takes the input of an array or a string for the left and right side of the equation
function balanceEquation($left, $right){
	
	//If input values are not arrays, make them into array
	if(!is_array($left)){
		$left = splitEquation($left, 2);
	}
	if(!is_array($right)){
		$right = splitEquation($right, 2);
	}
	
	//Create an array to hold all values
	$full = array_merge($left, $right);
	
	//Create composition matrix
	
	//Array to hold the elements
	$elements = array();
	
	//Array to hold the molecules
	$molecules = array();
	
	//Place all of the individual types of elements into an array organized by the order they first appear
	/** NOTE: Maybe can condense this code with iterator? If you see a way please do. Trying to avoid fractals of bad design. **/
	foreach($left as $molecule){
		
		$rawAtoms = preg_split('/(?=[A-Z])/', $molecule);
		
		//Split molecule at every capital letter
		//$rawAtoms = splitEquation($molecule);
		
		$atoms = array();
		
		//Split into appropriate number of atoms
		foreach($rawAtoms as $rawAtom){
			$splitAtom = splitEquation($rawAtom);
			
			foreach($splitAtom as $atom){
				$atoms[] = $atom;
			}
		}
		
		$molecules[] = $atoms;
		
		foreach($atoms as $atom){
			if(!in_array($atom, $elements)){
				$elements[] = $atom;
			}
		}
		
	}
	
	foreach($right as $molecule){
		
		$rawAtoms = preg_split('/(?=[A-Z])/', $molecule);
		
		//Split molecule at every capital letter
		//$rawAtoms = splitEquation($molecule);
		
		$atoms = array();
		
		//Split into appropriate number of atoms
		foreach($rawAtoms as $rawAtom){
			$splitAtom = splitEquation($rawAtom);
			
			foreach($splitAtom as $atom){
				$atoms[] = $atom;
			}
		}
		
		$molecules[] = $atoms;
		
		foreach($atoms as $atom){
			if(!in_array($atom, $elements)){
				$elements[] = $atom;
			}
		}
		
	}
	

	//Form actual matrix
	$compMatrix = array();
	foreach($elements as $element){
		
		//Create array to hold the values the element for each molecule
		$toPush = array();
		
		//Fill array with values for each molecule -- filling the columns
		foreach($molecules as $molecule){
			
			$arrayCounts = array_count_values($molecule);
			if(isset($arrayCounts[$element])){
				$toPush[] = $arrayCounts[$element];
			}else{
				$toPush[] = 0;
			}
			
		}
		$compMatrix[] = $toPush;
	}
	
	printMatrix($compMatrix, 'Composition Matrix', $full, $elements);
	
	//Create matrix in Reduced Row Echelon Form
	$reducedMatrix = rref($compMatrix);

	printMatrix($reducedMatrix, 'Reduced Row Echelon Form', $full, $elements);
	
	//Create square matrix with diagonal of ones
	$rows = count($reducedMatrix);
	$columns = count($reducedMatrix[0]);
	
	while($columns > $rows){
		//If there are more columns than rows, make the matrix square by adding 0's with a 1 on the diagonal
		$toPush = array();
		for($i = 0; $i < $columns; $i++){
			if($i == $columns - 1){
				$toPush[] = 1;
			}else{
				$toPush[] = 0;
			}
		}
		$reducedMatrix[] = $toPush;
		$rows++;
		
	}
	
	$addedColumns = false;
	
	if($rows > $columns){
		$_SESSION['errors'][] = 'The equation cannot be balanced.'; 
		return false;
	}
	
	
	//Print out the new table
	printMatrix($reducedMatrix, 'Augmented Matrix');
	
	$identityMatrix = identity_matrix($columns);
	
	//Make sure the inverse can be taken
	if($identityMatrix == $reducedMatrix){
		return false;
	}
	
	//Print out the new table
	printMatrix($identityMatrix, 'Identity Matrix');
	
	//Add identity matrix to first matrix
	$mergedMatrix = array();
	
	for($i = 0; $i < count($reducedMatrix); $i++){
		
		$toPush = array();
		
		foreach($reducedMatrix[$i] as $column){
			$toPush[] = $column;
		}
		
		foreach($identityMatrix[$i] as $column){
			$toPush[] = $column;
		}
		
		$mergedMatrix[] = $toPush;
	}
	
	//Print out the new table
	printMatrix($mergedMatrix, 'Merged Matrix');
	
	//Reduce the merged matrix
	$inverseMatrix = array();
	$columnsMerged = count($mergedMatrix[0]);
	$rows = count($mergedMatrix);
	for($j = 0; $j < $rows; $j++){
		
		$toPush = array();
		for($i = 0; $i < $columnsMerged; $i++){
			
			if($j == $rows - 1){
				$toPush[] = $mergedMatrix[$j][$i];
			}else{
				
			$toPush[] = $mergedMatrix[$j][$i] - $mergedMatrix[$rows - 1][$i] * $mergedMatrix[$j][$columns - 1];
			
			}
			
		}
		$inverseMatrix[] = $toPush;
	}
	
	//Print out the new table
	printMatrix($inverseMatrix, 'Inverse Matrix (the right half)');
	
	$rawCoefficients = array();
	foreach($inverseMatrix as $row){
		$rawCoefficients[] = array_pop($row);
	}

	$denominator = 1;
	foreach($rawCoefficients as $coeff){
		
		$coeff = abs($coeff);
		if( (int)$coeff == $coeff){
			continue;
		}
		
		//If it is a decimal, put it into fraction form
		if(is_float($coeff)){
			
			$fraction = float2rat(abs($coeff));
			$A = array_values(array_filter(explode('/', $fraction)));
			$denominator = lcm($denominator, $A[1]);
		}
	}
	
	$coefficients = array();
	foreach($rawCoefficients as $coeff){
		$coefficients[] = abs($coeff) * $denominator;
	}
	
	//Append coefficients to original equation
	for($i = 0; $i < count($left); $i++){
		if($coefficients[$i] > 1){
			$left[$i] = "$coefficients[$i]$left[$i]";
		}
	}
	
	$j = 0;
	for($i = count($left); $i < count($coefficients); $i++){
		
		if($coefficients[$i] > 1){
			$right[$j] = "$coefficients[$i]$right[$j]";
		}
		$j++;
	}
	
	$reactantString = implode(' + ', $left);
	$productString = implode(' + ', $right);
	
	return formatEquation($reactantString) . ' --> ' . formatEquation($productString);
	
}

function printMatrix($matrix, $title, $x = null, $y = null){
	
	$output =  "<p>$title</p><table class = 'table'><tr>\n";
	if( ($x) && ($y) ){
		$output .=  "<tr><td></td>\n";
		foreach($x as $molecule){
			$output .= "\n<td>$molecule</td>";
		}
		$output .= "\n</tr>";
		
	}
	
	
	$num = 0;
	
	foreach($matrix as $row) {
		
		$output .= '<tr>';
		
		if($x && $y){
			$output .= "<td>$y[$num]</td>";
			$num++;
		}
		
		
		foreach($row as $cell) {
			
			if( ($cell > 0) && (!is_int($cell)) ){
				$cell = float2rat($cell);
				if($cell == "1/1"){
					$cell = 1;
				}
			}
			if( ($cell < 0) && (!is_int($cell)) ){
				$cell = "-" . float2rat(abs($cell));
			}
			
			$output .=('<td>' . $cell . '</td>');
		}
		$output .= '</tr>';
  	}
	$output .= '</table>';
	$_SESSION['work'][] = $output;
}

//Put matrix into reverse row echelon form
//Code hijacked from www.rosettacode.com, full credit to them
function rref($matrix)
{
    $lead = 0;
    $rowCount = count($matrix);
    if ($rowCount == 0)
        return $matrix;
    $columnCount = 0;
    if (isset($matrix[0])) {
        $columnCount = count($matrix[0]);
    }
    for ($r = 0; $r < $rowCount; $r++) {
        if ($lead >= $columnCount)
            break;        {
            $i = $r;
            while ($matrix[$i][$lead] == 0) {
                $i++;
                if ($i == $rowCount) {
                    $i = $r;
                    $lead++;
                    if ($lead == $columnCount)
                        return $matrix;
                }
            }
            $temp = $matrix[$r];
            $matrix[$r] = $matrix[$i];
            $matrix[$i] = $temp;
        }        {
            $lv = $matrix[$r][$lead];
            for ($j = 0; $j < $columnCount; $j++) {
                $matrix[$r][$j] = $matrix[$r][$j] / $lv;
            }
        }
        for ($i = 0; $i < $rowCount; $i++) {
            if ($i != $r) {
                $lv = $matrix[$i][$lead];
                for ($j = 0; $j < $columnCount; $j++) {
                    $matrix[$i][$j] -= $lv * $matrix[$r][$j];
                }
            }
        }
        $lead++;
    }
    return $matrix;
}


//Create an identity matrix with the specified dimensions
function identity_matrix($dimension)
{
	$I = array();
	for ($i = 0; $i < $dimension; ++ $i) {
		for ($j = 0; $j < $dimension; ++ $j) {
			$I[$i][$j] = ($i == $j) ? 1 : 0;
		}
	}
	return $I;
}

?>