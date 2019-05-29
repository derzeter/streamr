<?php

/* Class to post data to StreamR - derzeter */
/* to be full , would need  
   - token expiration handling
   - reusing same token if possible
   - errors handling for GetToken method
   - errors handling for Postdata method
   - curl retry in GetToken method
   - curl retry in Postdata method    
   - data sanitazion against whatever (Postdata method)

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


        function GetToken() {

                $fields_string = "";

                $fields = array(
                        'apiKey' => urlencode($this->api_key)
                );

                foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; } rtrim($fields_string, '&');

                $ch = curl_init();

                curl_setopt($ch,CURLOPT_URL, $this->token_url);
                curl_setopt($ch,CURLOPT_POST, count($fields));
                curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

                $result = curl_exec($ch);

                if($result === FALSE) {
                   die(curl_error($ch));
                }

                curl_close($ch);

                $this->token = json_decode($result,true);

		// Uncomment this line if you want to see whats replied
                //print_r($this->token); 

        }


        function Postdata($data) {

                // Make sure data is the json format of the variables sent to streamr
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
                    die(curl_error($ch));
                }

                curl_close($ch);

		// Uncomment this line if you want to see whats replied
                //print_r($result); 


        }

}

?>
