<?php
/**
 * Marathon Pacing Calculator Ajax-request handler
 *
 * Included by wp-admin/admin-ajax.php, which receives the
 * original ajax request
 */

 // For logged-in users...
add_action( 'wp_ajax_retrieve_personalised_print_details_from_ID', 'retrieve_personalised_print_details_from_ID_and_postcode' );
// For non-logged in users...
add_action( 'wp_ajax_nopriv_retrieve_personalised_print_details_from_ID', 'retrieve_personalised_print_details_from_ID_and_postcode' );

add_action( 'wp_ajax_update_personalised_print_details', 'update_personalised_print_details' );
add_action( 'wp_ajax_nopriv_update_personalised_print_details', 'update_personalised_print_details' );

function retrieve_personalised_print_details_from_ID_and_postcode() {
    
    try {
        //Required if we're returning json
        header('Content-Type: application/json');

        // The $_REQUEST contains all the data sent via ajax
        if ( isset($_REQUEST) ) {
            //Get the inputs
            $ID = validate_field('ID',true, 5);
            $postcode = validate_field('postcode',true, 16);
            
            $details = array();
            query_db_for_print_details_from_ID($ID, $details);

            //Check that the postcode supplied matches the one we have stored (as a security check)
            if (!postcodes_match($details['postcode'],$postcode)) {
                throw new InvalidDataException('Incorrect postcode');
            } 
            
            // Now we'll return it to the javascript function
            // Anything outputted will be returned in the response
            echo json_encode(array(
                'firstName' => $details['firstName'],
                'lastName' => $details['lastName'],
                'address' => $details['address'],
                'postcode' => $details['postcode'],
                'phoneNumber' => $details['phoneNumber'],
                'status'=> 'success'));
        }

        // Always die in functions echoing ajax content
        die();
    }
    catch (InvalidDataException $e) {
        echo json_encode(array(
            'message' => $e->getMessage(),
            'status'=> 'error'));
        // Always die in functions echoing ajax content
        die(); 
    }
    catch (Exception $e) {
        //Any exception we didn't expect 
       $message = ($_SERVER['HTTP_HOST'] == "127.0.0.1:82" ? $e->getMessage() : 'Something went wrong whilst updating details');
        //Log error to php error log
        error_log('\'' . $e->getMessage() . '\' in file ' . $e->getFile() . ' at line ' . $e->getLine() . 'Trace: ' . $e->getTraceAsString(),0);
        echo json_encode(array(
            'message' => $message,
            'status'=> 'error'));
        // Always die in functions echoing ajax content
        die(); 
    }
}

function query_db_for_print_details_from_ID($ID, & $details) {
	$db = get_database_connection();
	
	//Use prepared statement with bound parameter to secure against sql injection
    $stmt = $db->prepare('SELECT * FROM orders WHERE ID = ?');
    $stmt->bind_param('s', $ID);
    $stmt->execute();
    $result = $stmt->get_result();
    
	$number_of_results = $result->num_rows;
    if ($number_of_results == 0) {
        throw new InvalidDataException("No matching order");    
    }
    
	$row = $result->fetch_assoc();
    $details['firstName'] = $row['FirstName'];
    $details['lastName'] = $row['LastName'];
    $details['address'] = $row['Address'];
    $details['postcode'] = $row['Postcode'];
    $details['phoneNumber'] = $row['PhoneNumber'];
	
	//Free up results object
	$result->free();
	$db->close();
}


function update_personalised_print_details() {
    
    try {
        //Required if we're returning json
        header('Content-Type: application/json');

        // The $_REQUEST contains all the data sent via ajax
        if ( isset($_REQUEST) ) {

            //Get the inputs
            $ID = validate_field('ID',true, 5);
            $postcode = validate_field('postcode',true, 16);
            $details = array();
            $details['messageLine1'] = validate_field('messageLine1',true, 50);
            $details['messageLine2'] = validate_field('messageLine2',false, 50);
            $details['messageLine3'] = validate_field('messageLine3',false, 50);
            /*$details['messageLine4'] = validate_field('messageLine4',false, 50);
            $details['messageLine5'] = validate_field('messageLine5',false, 50);
            $details['messageLine6'] = validate_field('messageLine6',false, 50);*/
            
            update_db_with_print_details_for_ID2($ID, $postcode, $details);
                        
            // Now we'll return it to the javascript function
            // Anything outputted will be returned in the response
            echo json_encode(array(
                'status'=> 'success'));
        }
        // Always die in functions echoing ajax content
        die();

    } catch (Exception $e) {
        $message = ($_SERVER['HTTP_HOST'] == "127.0.0.1:82" ? $e->getMessage() : 'Something went wrong whilst updating details');
        //TODO Log error to file
        echo json_encode(array(
            'message' => $message,
            'status'=> 'error'));
        // Always die in functions echoing ajax content
        die();
    }
}

function update_db_with_print_details_for_ID2($ID, $postcode, $details) {    
    $db = get_database_connection();
  
    //Check that the postcode supplied matches the one we have stored (as a security check)
    $stmt = $db->prepare('SELECT * FROM orders WHERE ID = ?');
    $stmt->bind_param('s', $ID);
    $stmt->execute();
    $result = $stmt->get_result();
    $number_of_results = $result->num_rows;
    if ($number_of_results == 0) {
        throw new InvalidDataException("No matching order");
    }
    
	$row = $result->fetch_assoc();
    $db_postcode = $row['Postcode'];
    
    if (!postcodes_match($db_postcode,$postcode)) {
        throw new InvalidDataException('Incorrect postcode');
    } 

    //Now do the update
	//Use prepared statement with bound parameter to secure against sql injection
/*
    $stmt = $db->prepare('UPDATE orders SET MessageLine1 = ?, MessageLine2 = ?, MessageLine3 = ?, MessageLine4 = ?, MessageLine5 = ?, MessageLine6 = ? WHERE ID = ?');
    $stmt->bind_param('sssssss', $details['messageLine1'], $details['messageLine2'], $details['messageLine3'], $details['messageLine4'], $details['messageLine5'], $details['messageLine6'], $ID);
*/
    $stmt = $db->prepare('UPDATE orders SET MessageLine1 = ?, MessageLine2 = ?, MessageLine3 = ? WHERE ID = ?');
    $stmt->bind_param('ssss', $details['messageLine1'], $details['messageLine2'], $details['messageLine3'], $ID);
    $stmt->execute();
    
    $db->close();     
}



function postcodes_match($postcode1, $postcode2) {
    //Case-insensitive and space-insensitive match
    return (str_replace(' ','',strtoupper($postcode1)) === str_replace(' ','',strtoupper($postcode2)));
}

function validate_field($field, $required, $maxlength) {
    if (!isset($_REQUEST[$field])) {
        throw new InvalidDataException($field . ' field is not set');    
    }
    if ($_REQUEST[$field] === '' && $required) {
        throw new InvalidDataException($field . ' field is empty');    
    }
    if (strlen($_REQUEST[$field] > $maxlength)) {
        throw new InvalidDataException($field . ' field is too long (length is ' . strlen($_REQUEST[$field]) . ')');    
    }
    return $_REQUEST[$field];
}

function get_database_connection() {
    //Tell mysqli to throw exceptions so we don't need to check return values
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    //http://codular.com/php-mysqli
    if ($_SERVER['HTTP_HOST'] == "127.0.0.1:82")
	{
		return new mysqli('127.0.0.1', 'root', 'password', 'personalised_print_details');			
	}
	else
	{
		//$db = new mysqli('10.169.0.50', 'flyingru_calc_ro', 'C4jQIiAmGaJ4', 'flyingru_calculator_v1');			
	}
}

class InvalidDataException extends Exception {}