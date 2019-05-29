<?php 

require("StreamR/StreamR.php");

/*

Example of posting to data stream of streamr.com 
$data contains an example of message you can send -> JSON Format please
$data must match the "Configure" section from your stream.
API Key can be found or in Profile, or in the API Access section of your stream.
Stream ID can be found in the details section of your stream.

*/

$data = '{"date":"'.gmdate("Y-m-d\TH:i:s\Z").'","data":"TEST-'.md5(rand(0,65535)).'"}';
$StreamR = new StreamR;

// Configure your stream using your api_key and stream_id
// Note that you can replace and set them into function Configuration() from StreamR/StreamR.php

$api_key  = "EwOgLyt2R7ST1n67g7QjFgBMed9w_lT62T4DMTZHSnCA";                       // Replace EwOgLyt2R7ST1n67g7QjFgBMed9w_lT62T4DMTZHSnCA by your API key
$stream_id = "JzYcmMY6RcSCkk3x6Aglrw";                                            // Replace JzYcmMY6RcSCkk3x6Aglrw by your stream id

// Call configuration 

$StreamR->Configuration($api_key,$stream_id);

// Create a token with StreamR to be able to POST

try {
	$StreamR->GetToken();
} catch (Exception $e) {
	echo 'Caught exception: ',  $e->getMessage(), "\n";
	print_r($StreamR->LastError());	
	die();
}


// Once you have the token, you can post.

try {
	$StreamR->PostData($data);
} catch (Exception $e) {
	echo 'Caught exception: ',  $e->getMessage(), "\n";
	print_r($StreamR->LastError());
	die();
}



?>
