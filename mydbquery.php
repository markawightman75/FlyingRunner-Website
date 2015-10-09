<?php
/*
$servername = "127.0.0.1:3306";
$username = "root";
$password = "password";
define('NEW_DB', 'flyingrunner');
*/

/*
//COPY OF v2 - we're changing this
$servername = 91.208.99.2:1100
$username = "flyingru_mig";
$password = "$dTl+|hQP~a^P25)";
define('NEW_DB', 'flyingru_mig');


*/

//LIVE DB but with user who only has SELECT privileges
$servername = "91.208.99.2:1065";
$username = "flyingru_mig";
$password = "$dTl+|hQP~a^P25)";
//$username = "flyingru_db";
//$password = "nu5WmDvkb7d";
define('NEW_DB', 'flyingru_db');


define('OLD_DB', 'source');

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully" . PHP_EOL;

mysqli_select_db($conn, NEW_DB);
// Perform queries
$result = mysqli_query($conn,"SELECT user_login FROM `wp_users` WHERE user_login LIKE '%wightman'");

if ($result)
  {
  // Return the number of rows in result set
  $rowcount=mysqli_num_rows($result);
  printf("Result set has %d rows.\n",$rowcount);
  // Free result set
  mysqli_free_result($result);
  }

//mysqli_query($con,"INSERT INTO Persons (FirstName,LastName,Age) VALUES ('Glenn','Quagmire',33)");


//flyingrunner
?> 