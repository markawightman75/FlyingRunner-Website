<?php
/**
 * Marathon Pacing Calculator Ajax-request handler
 *
 * Included by wp-admin/admin-ajax.php, which receives the
 * original ajax request
 */
 
 // For logged-in users...
add_action( 'wp_ajax_example_ajax_request', 'example_ajax_request' );
// For non-logged in users...
add_action( 'wp_ajax_nopriv_example_ajax_request', 'example_ajax_request' );

function example_ajax_request() {
 
    // The $_REQUEST contains all the data sent via ajax
    if ( isset($_REQUEST) ) {
     
		//Get the inputs
        $age_category = $_REQUEST['age_category'];
        $previous_marathons = $_REQUEST['previous_marathons'];
		$finish_time_from = $_REQUEST['finish_time_from'];
		$finish_time_to = $_REQUEST['finish_time_to'];
		$gender = $_REQUEST['gender'];
		$prediction_accuracy_tag = $_REQUEST['prediction_accuracy_tag'];
		
		//Do a look up and calculations from the inputs		
		$split_means = array();
		$splits_cumulative_hms = array();
		$speeds_min_per_km = array();
		$speeds_min_per_mile = array();
		$speeds_sec_per_mile = array();
		$runners_details = array();
		$debug_array = lookup_splits($age_category, $previous_marathons, $gender, $finish_time_from, $finish_time_to, $prediction_accuracy_tag, $number_of_runners, $runners_details, $split_means, $splits_cumulative_hms, $speeds_min_per_km, $speeds_min_per_mile, $speeds_sec_per_mile);
		$debug = 'SPLITS COUNT: ' . count($splits_cumulative_hms);
		foreach ($debug_array as $debug_line) {
			$debug = $debug . "<p style=\"margin-bottom: 0px;\">" . $debug_line . "</p>";
		}
		
		//Required if we're returning json
		header('Content-Type: application/json');
		
        // Now we'll return it to the javascript function
        // Anything outputted will be returned in the response
		echo json_encode(array(
			'number_of_runners' => $number_of_runners,
			'runners_details' =>  json_encode($runners_details),
			'split_means' => json_encode($split_means),
			'splits_cumulative' => json_encode($splits_cumulative_hms),
			'speeds_min_per_km' => json_encode($speeds_min_per_km),
			'speeds_min_per_mile' => json_encode($speeds_min_per_mile),
			'speeds_sec_per_mile' =>  json_encode($speeds_sec_per_mile),
			'debug' => $debug,
			'status'=> 'Success'));

		
        // If you're debugging, it might be useful to see what was sent in the $_REQUEST
        //print_r($_REQUEST);
     
    }
     
    // Always die in functions echoing ajax content
   die();
}

/*Cumulative splits parameter is an array that should be created and passed in (parameter is defined as reference) */
/*Returns debug message*/
function lookup_splits($age_category, $previous_marathons, $gender, $finish_time_from, $finish_time_to, $prediction_accuracy_tag, 
& $number_of_runners, & $results, & $split_means, & $splits_cumulative_hms, & $speeds_min_per_km, & $speeds_min_per_mile, & $speeds_sec_per_mile) {
//http://codular.com/php-mysqli
	$debug = array();

	//Connect to server/database
	if ($_SERVER['HTTP_HOST'] == "127.0.0.1:82")
	{
		$db = new mysqli('127.0.0.1', 'root', 'password', 'marathon_pacing_calculator_v1');			
	}
	else
	{
		$db = new mysqli('10.169.0.50', 'flyingru_calc_ro', 'C4jQIiAmGaJ4', 'flyingru_calculator_v1');			
	}
	
	if($db->connect_errno > 0){
		//TODO: Establish what this does...
		//TODO: Free up if return leaves here
		$debug[] = 'Unable to connect to database [' . $db->connect_error . ']';
		return $debug;
	}	
	
	//Find runners with matching finish time (+/- range) and get their splits
	//$age_category = "40-49";
	//$previous_marathons = ">10";
	
	//Convert $target_time (in format hh:mm:ss) to seconds
	//$str_time = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $target_time);
	//sscanf($str_time, "%d:%d:%d", $hours, $minutes, $seconds);
	//$finish_time = $hours * 3600 + $minutes * 60 + $seconds;
	
	//$finish_time = 10800; //3 hours   (4 hours = 14400)
	//$finish_time_min = $finish_time - ($ran_within_minutes_of_this_target_time * 60); //- 5 minutes
	//$finish_time_max = $finish_time + ($ran_within_minutes_of_this_target_time * 60); //+ 5 minutes
	
	$slower_than_prediction_min = 0 - ($ran_within_minutes_of_prediction * 60);
	$slower_than_prediction_max = $ran_within_minutes_of_prediction * 60;
	
/* 	$sql = "SELECT * FROM lookup WHERE 
		Age_category = '" . $age_category . "' AND 
		Experience_races = '" . $previous_marathons . "' AND 
		Finish_time_s >=" . strval($finish_time_min) . " AND 
		Finish_time_s <=" . strval($finish_time_max);
 */
 	
	$sql = "SELECT Runner_id, Name, Experience_races, Age_category, Gender,
		Finish_time, Finish_time_s, Prediction_hh_mm, Slower_than_prediction_by_s,
		Prediction_accuracy_percent,
		5k_Split_s, 10k_Split_s, 15k_Split_s, 20k_Split_s, 25k_Split_s, 30k_Split_s, 35k_Split_s, 40k_Split_s
		FROM lookup 
		WHERE 
		Finish_time_s >=" . strval($finish_time_from) . " AND 
		Finish_time_s <=" . strval($finish_time_to);
//		AND
		//Slower_than_prediction_by_s >=" . strval($slower_than_prediction_min) . " AND
		//Slower_than_prediction_by_s <=" . strval($slower_than_prediction_max);

		//, Slower_than_prediction_by_%
		
	if ($age_category != 'Any') {
		$sql = $sql . " AND Age_category = '" . $age_category . "'";
	}
	if ($previous_marathons != 'Any') {
		$sql = $sql . " AND Experience_races = '" . $previous_marathons . "'";		
	}
	if ($gender != 'Any') {
		$sql = $sql . " AND Gender = '" . $gender . "'";		
	}
	if ($prediction_accuracy_tag != 'Any') {
		if ($prediction_accuracy_tag == '99+') $sql = $sql . " AND Prediction_accuracy_percent >= 99.0 ";		
		if ($prediction_accuracy_tag == '95+') $sql = $sql . " AND Prediction_accuracy_percent >= 95.0 ";		
		if ($prediction_accuracy_tag == '90+') $sql = $sql . " AND Prediction_accuracy_percent >= 90.0 ";		
		if ($prediction_accuracy_tag == '-90') $sql = $sql . " AND Prediction_accuracy_percent < 90.0 ";		
	}
	
	$sql = $sql . " ORDER BY Prediction_accuracy_percent DESC";
	$debug[] = $sql;
	
	if(!$result = $db->query($sql)){	
		$debug[] = 'There was an error running the query [' . $db->error . ']';
		return $debug;
	}
	
	
	$number_of_results = $result->num_rows;
	$number_of_runners = $number_of_results;
	
	$debug = array();
	$debug[] = "Debug information:";
	$debug[] = "Age category: " . $age_category;
	$debug[] = "Previous marathons: " . $previous_marathons;
	$debug[] = "Finish time: " . $finish_time_min . " to " . $finish_time_max;
	$debug[] = "Ran within minutes of Finish time: " . $ran_within_minutes_of_this_target_time;
	$debug[] = "Ran within minutes of their prediction: " . $ran_within_minutes_of_prediction;
	$debug[] = "Total results: " . $result->num_rows;
			
		
	$results = array();
	$result_index = 0;
	while($row = $result->fetch_assoc()){
		$name = $row['Name'];
		$name_parts = explode(" ",$name);
		$initials = "";
		foreach ($name_parts as $name_part) {
			//$initials += substr($name_part,0,1);
			$initials = $initials . $name_part[0];
		}
		//$initials = $name_parts[0]; 
		$results['initials'][$result_index] = $initials;
		$results['finish-time'][$result_index] = $row['Finish_time'];
		$results['finish-time-s'][$result_index] = $row['Finish_time_s'];
		$results['predicted-time'][$result_index] = $row['Prediction_hh_mm'];
		//$results['slower-than-prediction-by-%'][$result_index] = round($row['Slower_than_prediction_by_%']);
		$results['prediction-accuracy-percent'][$result_index] = $row['Prediction_accuracy_percent'];
		
		$results['age-category'][$result_index] = $row['Age_category'];
		$results['gender'][$result_index] = $row['Gender'];
		$results['previous-marathons'][$result_index] = $row['Experience_races'];
		$results['5k_Split_s'][$result_index] = $row['5k_Split_s'];
		$results['10k_Split_s'][$result_index] = $row['10k_Split_s'];
		$results['15k_Split_s'][$result_index] = $row['15k_Split_s'];
		$results['20k_Split_s'][$result_index] = $row['20k_Split_s'];
		$results['21k_Split_s'][$result_index] = $row['21k_Split_s'];
		$results['25k_Split_s'][$result_index] = $row['25k_Split_s'];
		$results['30k_Split_s'][$result_index] = $row['30k_Split_s'];
		$results['35k_Split_s'][$result_index] = $row['35k_Split_s'];
		$results['40k_Split_s'][$result_index] = $row['40k_Split_s'];
		$result_index++;
	}

	$fivek_splits_total = 0;
	$tenk_splits_total = 0;
	$split_totals = array(); //The sum of each split for all runners (i.e. 5k for runner 1 + 5k for runner 2...)
	for ($runner = 0; $runner < $number_of_results; $runner++) {
		$debug[] = $results['initials'][$runner] . "..........5K split: " . $results['5k_Split_s'][$runner] . ".........10K split: " . $results['10k_Split_s'][$runner];
		//We need to normalise all split times to create a meaningful average
		//Normalise them to 4 hour pace (14400 seconds)		
		$normalise_factor = 14400 / ($results['finish-time-s'][$runner]);
		
		$split_totals['5k'] += ($results['5k_Split_s'][$runner] * $normalise_factor);
		$split_totals['10k'] += ($results['10k_Split_s'][$runner] * $normalise_factor);
		$split_totals['15k'] += ($results['15k_Split_s'][$runner] * $normalise_factor);
		$split_totals['20k'] += ($results['20k_Split_s'][$runner] * $normalise_factor);
		$split_totals['21k'] += ($results['21k_Split_s'][$runner] * $normalise_factor);
		$split_totals['25k'] += ($results['25k_Split_s'][$runner] * $normalise_factor);
		$split_totals['30k'] += ($results['30k_Split_s'][$runner] * $normalise_factor);
		$split_totals['35k'] += ($results['35k_Split_s'][$runner] * $normalise_factor);
		$split_totals['40k'] += ($results['40k_Split_s'][$runner] * $normalise_factor);
	}
	
	//Means are in SECONDS
	$split_means = array(); //The mean of each split (i.e. the sum of all 5k splits / number of runners)
	$split_means['5k'] = round($split_totals['5k'] / $number_of_results);
	$split_means['10k'] = round($split_totals['10k'] / $number_of_results);
	$split_means['15k'] = round($split_totals['15k'] / $number_of_results);
	$split_means['20k'] = round($split_totals['20k'] / $number_of_results);
	$split_means['21k'] = round($split_totals['21k'] / $number_of_results);
	$split_means['25k'] = round($split_totals['25k'] / $number_of_results);
	$split_means['30k'] = round($split_totals['30k'] / $number_of_results);
	$split_means['35k'] = round($split_totals['35k'] / $number_of_results);
	$split_means['40k'] = round($split_totals['40k'] / $number_of_results);
	
	//$splits_cumulative = array(); //The cumulative time for each split
	$splits_cumulative['5k'] = $split_means['5k'];
	$splits_cumulative['10k'] = $split_means['5k'] + $split_means['10k'];
	$splits_cumulative['15k'] = $split_means['5k'] + $split_means['10k'] + $split_means['15k'];
	$splits_cumulative['20k'] = $split_means['5k'] + $split_means['10k'] + $split_means['15k'] + $split_means['20k'];
	$splits_cumulative['21k'] = $split_means['5k'] + $split_means['10k'] + $split_means['15k'] + $split_means['20k'] + $split_means['21k'];
	$splits_cumulative['25k'] = $split_means['5k'] + $split_means['10k'] + $split_means['15k'] + $split_means['20k'] + $split_means['25k'];
	$splits_cumulative['30k'] = $split_means['5k'] + $split_means['10k'] + $split_means['15k'] + $split_means['20k'] + $split_means['25k'] + $split_means['30k'] ;
	$splits_cumulative['35k'] = $split_means['5k'] + $split_means['10k'] + $split_means['15k'] + $split_means['20k'] + $split_means['25k'] + $split_means['30k'] + $split_means['35k'] ;
	$splits_cumulative['40k'] = $split_means['5k'] + $split_means['10k'] + $split_means['15k'] + $split_means['20k'] + $split_means['25k'] + $split_means['30k'] + $split_means['35k'] + $split_means['40k'];
		
		
	$splits_cumulative_hms['5k'] =  format_hh_mm_ss($splits_cumulative['5k']);
	$splits_cumulative_hms['10k'] =  format_hh_mm_ss($splits_cumulative['10k']);
	$splits_cumulative_hms['15k'] =  format_hh_mm_ss($splits_cumulative['15k']);
	$splits_cumulative_hms['20k'] =  format_hh_mm_ss($splits_cumulative['20k']);
	$splits_cumulative_hms['21k'] =  format_hh_mm_ss($splits_cumulative['21k']);
	$splits_cumulative_hms['25k'] =  format_hh_mm_ss($splits_cumulative['25k']);
	$splits_cumulative_hms['30k'] =  format_hh_mm_ss($splits_cumulative['30k']);
	$splits_cumulative_hms['35k'] =  format_hh_mm_ss($splits_cumulative['35k']);
	$splits_cumulative_hms['40k'] =  format_hh_mm_ss($splits_cumulative['40k']); 
	
	$speeds_min_per_km = array();
	$speeds_min_per_km['5k'] =  format_mm_ss($split_means['5k'] / 5);
	$speeds_min_per_km['10k'] = format_mm_ss($split_means['10k'] / 5);
	$speeds_min_per_km['15k'] = format_mm_ss($split_means['15k'] / 5);
	$speeds_min_per_km['20k'] = format_mm_ss($split_means['20k'] / 5);
	$speeds_min_per_km['25k'] = format_mm_ss($split_means['25k'] / 5);
	$speeds_min_per_km['30k'] = format_mm_ss($split_means['30k'] / 5);
	$speeds_min_per_km['35k'] = format_mm_ss($split_means['35k'] / 5);
	$speeds_min_per_km['40k'] = format_mm_ss($split_means['40k'] / 5);

	/*Calculate minutes per mile formatted in mm:ss*/
	$speeds_min_per_mile = array();
	$speeds_min_per_mile['5k'] = format_mm_ss((round($split_means['5k'] / 5) * 1.60934)); 
	$speeds_min_per_mile['10k'] = format_mm_ss((round($split_means['10k'] / 5) * 1.60934)); 
	$speeds_min_per_mile['15k'] = format_mm_ss((round($split_means['15k'] / 5) * 1.60934)); 
	$speeds_min_per_mile['20k'] = format_mm_ss((round($split_means['20k'] / 5) * 1.60934)); 
	$speeds_min_per_mile['25k'] = format_mm_ss((round($split_means['25k'] / 5) * 1.60934)); 
	$speeds_min_per_mile['30k'] = format_mm_ss((round($split_means['30k'] / 5) * 1.60934)); 
	$speeds_min_per_mile['35k'] = format_mm_ss((round($split_means['35k'] / 5) * 1.60934)); 
	$speeds_min_per_mile['40k'] = format_mm_ss((round($split_means['40k'] / 5) * 1.60934)); 
	
	/*Calculate seconds per mile (for charting) left as integer*/
	$speeds_sec_per_mile = array();
	$speeds_sec_per_mile['5k'] = round(($split_means['5k'] / 5) * 1.60934); 
	$speeds_sec_per_mile['10k'] = round(($split_means['10k'] / 5) * 1.60934); 
	$speeds_sec_per_mile['15k'] = round(($split_means['15k'] / 5) * 1.60934); 
	$speeds_sec_per_mile['20k'] = round(($split_means['20k'] / 5) * 1.60934); 
	$speeds_sec_per_mile['25k'] = round(($split_means['25k'] / 5) * 1.60934); 
	$speeds_sec_per_mile['30k'] = round(($split_means['30k'] / 5) * 1.60934); 
	$speeds_sec_per_mile['35k'] = round(($split_means['35k'] / 5) * 1.60934); 
	$speeds_sec_per_mile['40k'] = round(($split_means['40k'] / 5) * 1.60934); 
	
	$debug[] = "5k split mean: " . $split_means['5k'];
	$debug[] = "5k split mean: " .   format_hh_mm_ss($split_means['5k']);
	$debug[] = "10k split mean: " .   format_hh_mm_ss($split_means['10k']);
	$debug[] = "5k: " .   format_hh_mm_ss($splits_cumulative['5k']);
	$debug[] = "10k: " .   format_hh_mm_ss($splits_cumulative['10k']);
	$debug[] = "15k: " .   format_hh_mm_ss($splits_cumulative['15k']);
	$debug[] = "20k: " .   format_hh_mm_ss($splits_cumulative['20k']);
	$debug[] = "25k: " .   format_hh_mm_ss($splits_cumulative['25k']);
	$debug[] = "30k: " .   format_hh_mm_ss($splits_cumulative['30k']);
	$debug[] = "35k: " .   format_hh_mm_ss($splits_cumulative['35k']);
	$debug[] = "40k: " .   format_hh_mm_ss($splits_cumulative['40k']);
	
	//Free up results object
	$result->free();
	$db->close();
	
	//Average their splits
		//$debug = "5k split 1: " . $fivek_splits[0];
	
	return $debug;

}

function format_hh_mm_ss($t,$f=':') // t = seconds, f = separator 
{
  return sprintf("%02d%s%02d%s%02d", floor($t/3600), $f, ($t/60)%60, $f, $t%60);
}

function format_mm_ss($t,$f=':') // t = seconds, f = separator 
{
  //return sprintf("%02d%s%02d", ($t/60)%60, $f, $t%60);
  return sprintf("%01d%s%02d", ($t/60)%60, $f, $t%60);
}

 // For logged-in users...
add_action( 'wp_ajax_create_pacing_band_ajax_request', 'create_pacing_band_ajax_request' );
// For non-logged in users...
add_action( 'wp_ajax_nopriv_create_pacing_band_ajax_request', 'create_pacing_band_ajax_request' );

function create_pacing_band_ajax_request() {
 // The $_REQUEST contains all the data sent via ajax
    if ( isset($_REQUEST) ) {
		//Get the inputs
        $mile_1_split = $_REQUEST['mile-1-split'];		
		
		require_once('create-pacing-band.php');
		create_pacing_band();
	
	}
	
	//Required if we're returning json
	header('Content-Type: application/json');
		
    // Now we'll return it to the javascript function
    // Anything outputted will be returned in the response
	//echo json_encode(array(
	//	'mile_1_split_return' => $mile_1_split,
	//	'status'=> 'Success'));

	  
    // Always die in functions echoing ajax content
    die();
}