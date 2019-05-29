<?php

/* Class to post data to StreamR - derzeter */
/* v 1.0 - 29/05/2019                       */

/* to be full , would need :
   - token expiration handling {done}
   - reusing same token if possible {it's possible by calling multiple times Postdata in the same script} {done}
   - curl retry in GetToken method {done}
   - curl retry in Postdata method {done}
   - data sanitazion against whatever (Postdata method) - let's check it is a json {done}
   - errors handling for GetToken method (from API) {done}
   - errors handling for Postdata method (from API) {done}
Otherwise, works pretty fine as of June 2019
*/


Class StreamR {

/* Class to post to streamr.com DATA, in PHP */
/* missing : Error handling */

private $api_key = "";
private $stream_id = "";
private $token_url = "";
private $post_url = "";
private $token = Array();
private $error = Array();
private $curl_limit = 5; // Retry limit for curl connection

        function Configuration($api_key="",$stream_id="") {

                $this->api_key  = $api_key;					                       // Replace by your API key
                $this->stream_id = $stream_id; 		                                               // Replace by your stream id

                $this->token_url = "https://www.streamr.com/api/v1/login/apikey";                       // Token URL (To change if it changes on streamr.com)
                $this->post_url = "https://www.streamr.com/api/v1/streams/".$this->stream_id."/data";   // Post URL (To change if it changes on streamr.com - it includes stream_id)

        }


        function __construct() {

                // Calling configuration if necessary and if parameters hidden within configuration
                $this->Configuration();
        }

	function isJson($string) {
		json_decode($string);
		return (json_last_error() == JSON_ERROR_NONE);
	}

        function GetToken() {

                $fields_string = "";
		$retries = 0;

                $fields = array(
                        'apiKey' => urlencode($this->api_key)
                );

                foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; } rtrim($fields_string, '&');

		do {

	                $ch = curl_init();

	                curl_setopt($ch,CURLOPT_URL, $this->token_url);
        	        curl_setopt($ch,CURLOPT_POST, count($fields));
                	curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
	            	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

	                $result = curl_exec($ch);

        	        if($result === FALSE) {

				$retries++;

				if($retries>=$this->curl_limit) {
					$this->error["error"] = "curl error";
					$this->error["call"] = "GetToken()";
					$this->error["actual"] = time();
					throw new Exception('Curl error.');
				}
	                } else $retries=0;

        	        curl_close($ch);

		} while($retries>0);

                $this->token = json_decode($result,true);

		// Uncomment this line if you want to see whats replied
                //print_r($this->token); 

		if(isset($this->token["code"])) {
			$this->error["error"] = "API error";
			$this->error["call"] = "GetToken()";
			$this->error["actual"] = time();
                        $this->error["details"] = $this->token;
			throw new Exception('API error.');
		}


        }


        function Postdata($data) {

		$retries = 0;
		$reply = "";

		// Checking json

		if(!$this->isJson($data)) {
				$this->error["error"] = "json error";
				$this->error["call"] = "Postdata()";
				$this->error["actual"] = time();
				throw new Exception('Json format error.');
		}

                // Make sure data is the json format of the variables sent to streamr
		// Checking if token is not expired yet

		do {

			if(time()>=strtotime($this->token["expires"])) {
				$this->error["error"] = "token expired";
				$this->error["expires"] = strtotime($this->token["expires"]);
				$this->error["call"] = "Postdata()";
				$this->error["actual"] = time();
				throw new Exception('Token Expired.');
			}

                // Creating URL

	                $ch = curl_init();

        	        curl_setopt($ch,CURLOPT_URL, $this->post_url);
               		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

               		$headers = Array();

	                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        	            'Authorization: Bearer '.$this->token["token"],
                	    'Content-Type: application/json',
                  	  'Content-Length: ' . strlen($data))
	                );

        	        $result = curl_exec($ch);

        	        if($result === FALSE) {

				$retries++;

				if($retries>=$this->curl_limit) {
					$this->error["error"] = "curl error";
					$this->error["call"] = "Postdata()";
					$this->error["actual"] = time();
					throw new Exception('Curl error.');
				}

	                } else $retries=0;

	                curl_close($ch);

		} while($retries>0); 

		$reply = json_decode($result,true);

		// Uncomment this line if you want to see whats replied
		//print_r($reply);

		if(isset($reply["error"])) {
			$this->error["error"] = "API error";
			$this->error["call"] = "Postdata()";
			$this->error["actual"] = time();
                        $this->error["details"] = $reply;
			throw new Exception('API error.');
		}


        }

        function LastError() {

		// Send back last error which occured 

		return $this->error;
	}

}

?>
