<?php

require_once('create-pacing-band.php');

$splits = array();
for ($m = 1; $m <= 26; $m++) {
    $splits['mile' . $m] = $_GET['mile' . $m];
} 
$splits['halfway'] = $_GET['halfway'];
$splits['finish'] = $_GET['finish'];
create_pacing_band($splits);

//File downloading code from http://ssdtutorials.com/tutorials/title/download-file-with-php.html


// block any attempt to the filesystem
/* if (isset($_GET['file']) && basename($_GET['file']) == $_GET['file']) {
    $filename = $_GET['file'];
} else {
    $filename = NULL;
}
// define error message
$err = '<p style="color:#990000">Sorry, the file you are requesting is unavailable.</p>';

 if (!$filename) {
        // if variable $filename is NULL or false display the message
        echo $err;
    } else {
        // define the path to your download folder plus assign the file name
        $path = 'downloads/'.$filename;
        // check that file exists and is readable
        if (file_exists($path) && is_readable($path)) {
            // get the file size and send the http headers
            $size = filesize($path);
            //header('Content-Type: application/octet-stream');
            header('Content-type: application/pdf');
			header('Content-Length: '.$size);
            header('Content-Disposition: attachment; filename=pacing-band.pdf');
            header('Content-Transfer-Encoding: binary');
			
            // open the file in binary read-only mode
            // display the error messages if the file can´t be opened
            $file = @ fopen($path, 'rb');
            if ($file) {
                // stream the file and exit the script when complete
                fpassthru($file);
                exit;
            } else {
                echo $err;
            }
        } else {
            echo $err;
        }
    }
	 */

?>