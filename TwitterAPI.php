<?php
function callTwitter($path, $params) {
	$token = '1657757666-I3JKznBOanRf0umtgZV41TSO5Q3QahYvBQ38PAZ';
	$token_secret = 'HyYZJfDBPDq5tDf0OjP1E9g6OLVHXZRGMJKROjHTEQAps';
	$consumer_key = '04P97ntBpPKRJ0g0jnnJJmnpN';
	$consumer_secret = 'sJcQaCsrptPOUXl3qfRTdBxA1SnYbeL263aiene74Q6h8N4WFH';

	$host = 'api.twitter.com/1.1/';
	$method = 'GET';
	


	$oauth = array(
    	'oauth_consumer_key' => $consumer_key,
    	'oauth_token' => $token,
    	'oauth_nonce' => (string)mt_rand(), // a stronger nonce is recommended
    	'oauth_timestamp' => time(),
    	'oauth_signature_method' => 'HMAC-SHA1',
    	'oauth_version' => '1.0'
	);

	$oauth = array_map("rawurlencode", $oauth); // must be encoded before sorting
	$params = array_map("rawurlencode", $params);

	$arr = array_merge($oauth, $params); // combine the values THEN sort

	asort($arr); // secondary sort (value)
	ksort($arr); // primary sort (key)

	// http_build_query automatically encodes, but our parameters
	// are already encoded, and must be by this point, so we undo
	// the encoding step
	$querystring = urldecode(http_build_query($arr, '', '&'));

	$url = "https://$host$path";

	// mash everything together for the text to hash
	$base_string = $method."&".rawurlencode($url)."&".rawurlencode($querystring);

	// same with the key
	$key = rawurlencode($consumer_secret)."&".rawurlencode($token_secret);

	// generate the hash
	$signature = rawurlencode(base64_encode(hash_hmac('sha1', $base_string, $key, true)));

	// this time we're using a normal GET query, and we're only encoding the query params
	// (without the oauth params)
	$url .= "?".http_build_query($params);
	
	$oauth['oauth_signature'] = $signature; // don't want to abandon all that work!
	ksort($oauth); // probably not necessary, but twitter's demo does it

	// also not necessary, but twitter's demo does this too
	function add_quotes($str) { return '"'.$str.'"'; }
	$oauth = array_map("add_quotes", $oauth);

	// this is the full value of the Authorization line
	$auth = "OAuth " . urldecode(http_build_query($oauth, '', ', '));

	// if you're doing post, you need to skip the GET building above
	// and instead supply query parameters to CURLOPT_POSTFIELDS
	$options = array( CURLOPT_HTTPHEADER => array("Authorization: $auth"),
    	              //CURLOPT_POSTFIELDS => $postfields,
    	              CURLOPT_HEADER => false,
       	    	      CURLOPT_URL => $url,
       		    	  CURLOPT_RETURNTRANSFER => true,
       	            CURLOPT_SSL_VERIFYPEER => false);

	// do our business
	$feed = curl_init();
	curl_setopt_array($feed, $options);
	$json = curl_exec($feed);
	curl_close($feed);

	$twitter_data = json_decode($json);
	return $twitter_data;
}
?>