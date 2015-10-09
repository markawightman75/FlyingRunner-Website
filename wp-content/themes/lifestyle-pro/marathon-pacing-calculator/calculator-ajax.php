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
		$target_time = $_REQUEST['target_time'];
		
		//Do a look up and calculations from the inputs		
		$splits_cumulative_hms = array();
		$debug_array = lookup_splits($age_category, $previous_marathons, $target_time, $number_of_runners, $splits_cumulative_hms);
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
			'splits_cumulative' => json_encode($splits_cumulative_hms),
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
function lookup_splits($age_category, $previous_marathons, $target_time, & $number_of_runners, & $splits_cumulative_hms) {
//http://codular.com/php-mysqli
	$debug = array();

	//Connect to server/database
	$db = new mysqli('127.0.0.1', 'root', 'password', 'marathon_pacing_calculator_v1');

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
	$str_time = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $target_time);
	sscanf($str_time, "%d:%d:%d", $hours, $minutes, $seconds);
	$finish_time = $hours * 3600 + $minutes * 60 + $seconds;
	
	//$finish_time = 10800; //3 hours   (4 hours = 14400)
	$finish_time_min = $finish_time - 240; //- 5 minutes
	$finish_time_max = $finish_time + 240; //+ 5 minutes
	
/* 	$sql = "SELECT * FROM lookup WHERE 
		Age_category = '" . $age_category . "' AND 
		Experience_races = '" . $previous_marathons . "' AND 
		Finish_time_s >=" . strval($finish_time_min) . " AND 
		Finish_time_s <=" . strval($finish_time_max);
 */
 	
	$sql = "SELECT * FROM lookup WHERE 
		Finish_time_s >=" . strval($finish_time_min) . " AND 
		Finish_time_s <=" . strval($finish_time_max);

	if ($age_category != 'Any') {
		$sql = $sql . " AND Age_category = '" . $age_category . "'";
	}
	if ($previous_marathons != 'Any') {
		$sql = $sql . " AND Experience_races = '" . $previous_marathons . "'";		
	}
	
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
	$debug[] = "Total results: " . $result->num_rows;
		
	$results = array();
	$result_index = 0;
	while($row = $result->fetch_assoc()){
		$results['name'][$result_index] = $row['Name'];
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
		$debug[] = $results['name'][$runner] . "..........5K split: " . $results['5k_Split_s'][$runner] . ".........10K split: " . $results['10k_Split_s'][$runner];
	
		
		$split_totals['5k'] += $results['5k_Split_s'][$runner];
		$split_totals['10k'] += $results['10k_Split_s'][$runner];
		$split_totals['15k'] += $results['15k_Split_s'][$runner];
		$split_totals['20k'] += $results['20k_Split_s'][$runner];
		$split_totals['21k'] += $results['21k_Split_s'][$runner];
		$split_totals['25k'] += $results['25k_Split_s'][$runner];
		$split_totals['30k'] += $results['30k_Split_s'][$runner];
		$split_totals['35k'] += $results['35k_Split_s'][$runner];
		$split_totals['40k'] += $results['40k_Split_s'][$runner];
	}
	
	//Means are in SECONDS
	$split_means = array(); //The mean of each split (i.e. the sum of all 5k splits / number of runners)
	$split_means['5k'] = $split_totals['5k'] / $number_of_results;
	$split_means['10k'] = $split_totals['10k'] / $number_of_results;
	$split_means['15k'] = $split_totals['15k'] / $number_of_results;
	$split_means['20k'] = $split_totals['20k'] / $number_of_results;
	$split_means['21k'] = $split_totals['21k'] / $number_of_results;
	$split_means['25k'] = $split_totals['25k'] / $number_of_results;
	$split_means['30k'] = $split_totals['30k'] / $number_of_results;
	$split_means['35k'] = $split_totals['35k'] / $number_of_results;
	$split_means['40k'] = $split_totals['40k'] / $number_of_results;
	
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
		
		
	$splits_cumulative_hms['5k'] =  format_time($splits_cumulative['5k']);
	$splits_cumulative_hms['10k'] =  format_time($splits_cumulative['10k']);
	$splits_cumulative_hms['15k'] =  format_time($splits_cumulative['15k']);
	$splits_cumulative_hms['20k'] =  format_time($splits_cumulative['20k']);
	$splits_cumulative_hms['21k'] =  format_time($splits_cumulative['21k']);
	$splits_cumulative_hms['25k'] =  format_time($splits_cumulative['25k']);
	$splits_cumulative_hms['30k'] =  format_time($splits_cumulative['30k']);
	$splits_cumulative_hms['35k'] =  format_time($splits_cumulative['35k']);
	$splits_cumulative_hms['40k'] =  format_time($splits_cumulative['40k']); 
	
	$debug[] = "5k split mean: " . $split_means['5k'];
	$debug[] = "5k split mean: " .   format_time($split_means['5k']);
	$debug[] = "10k split mean: " .   format_time($split_means['10k']);
	$debug[] = "5k: " .   format_time($splits_cumulative['5k']);
	$debug[] = "10k: " .   format_time($splits_cumulative['10k']);
	$debug[] = "15k: " .   format_time($splits_cumulative['15k']);
	$debug[] = "20k: " .   format_time($splits_cumulative['20k']);
	$debug[] = "25k: " .   format_time($splits_cumulative['25k']);
	$debug[] = "30k: " .   format_time($splits_cumulative['30k']);
	$debug[] = "35k: " .   format_time($splits_cumulative['35k']);
	$debug[] = "40k: " .   format_time($splits_cumulative['40k']);
	
	//Free up results object
	$result->free();
	$db->close();
	
	//Average their splits
		//$debug = "5k split 1: " . $fivek_splits[0];
	
	return $debug;

}

function format_time($t,$f=':') // t = seconds, f = separator 
{
  return sprintf("%02d%s%02d%s%02d", floor($t/3600), $f, ($t/60)%60, $f, $t%60);
}


