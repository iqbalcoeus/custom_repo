<?php 

//Conf
 
$dbSettings = array(
	'host'		 => 'localhost',
	'user'		 => 'root',
	'password'	 => 'coeus123',
	'database'	 => 'aig',
);
//Conf

//get All orders from aig_customer table
$orders = getOrders();

//processing and record updation
while($row=$orders->fetch_assoc()){
	
	$term= setTerm($row['term_length']);
	$coverage = setCoverage($row['face_amount']);

	if($term > 0){
		updateTerm($term, $row['order_id']);
	}
	if($coverage > 0){
		updateCoverage($coverage, $row['order_id']);
	}
}

echo "Done". PHP_EOL;

// // function setInsuranceType($order)
// // {

// // }

/**
 * corretly maps the Term value
 * @param int $termLength original term length
 *
 * @return int $term mapped value of term
 */

function setTerm($termLength)
{
	$term = 0;
	if($termLength >= 0 AND $termLength <= 9){
		$term=1;
	}
	elseif($termLength >= 10 AND $termLength <= 19){
		$term=2;
	}
	elseif($termLength >= 20 AND $termLength <= 29){
		$term=3;
	}
	elseif($termLength >= 30){
		$term=4;
	}
	else{
		logError('Term has invalid value', $order);
	}
	return $term;
}

/**
 * Corretly maps the coverage value
 * @param int $coverageAmount original coverage amount
 *
 * @return int $coverage mapped coverage value
 */
function setCoverage($coverageAmount)
{
	$coverage = 0;

	if($coverageAmount < 100000){
		$coverage = 1;
	}
	elseif($coverageAmount >= 100000 AND  $coverageAmount <= 249000){
		$coverage = 2;
	}
	elseif($coverageAmount >= 250000 AND  $coverageAmount <= 499000){
		$coverage = 3;
	}
	elseif($coverageAmount >=500000 AND  $coverageAmount <= 999000){
		$coverage=4;
	}
	elseif($coverageAmount >= 1000000){
		$coverage = 5;
	}
	else{
		logError('Coverage has invalid value', $order);
	}

	return $coverage;
}

/**
 * Updates the respective record in DB with mapped value of term
 * @param  int $term     mapped term value
 * @param  string $order_id order id 
 * 
 */
function updateTerm($term, $order_id)
{
	$mysqli = getMysqli();
	$sql= "UPDATE aig_reviews SET term=".$term." WHERE order_id='".$order_id."'";
	$mysqli->query($sql);
}

/**
 * Updates the respective record in DB with mapped value of Coverage
 * @param  int $coverage mapped value
 * @param  string $order_id order id
 * 
 */
function updateCoverage($coverage, $order_id)
{
	$mysqli = getMysqli();
	$sql= "UPDATE aig_reviews SET coverage=".$coverage." WHERE order_id='".$order_id."'";
	
	$mysqli->query($sql);
}

/**
 * get All orders from aig_csutomer table 
 * @return mysqli_result  $orders all record from DB
 */
function getOrders()
{
	$mysqli= getMysqli();
	$sql = "SELECT order_id, product_type, face_amount, term_length FROM aig_customers";
	$orders = $mysqli->query($sql);
	return $orders;
}

/**
 * Create a DB  conection 
 * @return mysqli $mysqli 
 */
function getMysqli()
{
	static $mysqli = NULL;

	if ($mysqli === NULL) {
		// Initiate singleton mysqli connection
		global $dbSettings;

		$mysqli = new mysqli(
			$dbSettings['host'], $dbSettings['user'], $dbSettings['password'], $dbSettings['database']
		);
	}

	return $mysqli;
}

?>