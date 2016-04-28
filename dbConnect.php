<?php

// set up connection parameters
$dbHost 		= 'localhost';
$databaseName 	= 'b00664468_302db';
$username 		= 'B00664468';
$password 		= 'jut8an';

// make the database connection
$db = new PDO("mysql:host=$dbHost;dbname=$databaseName;charset=utf8", "$username", "$password");
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 	// enable error handling
$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); 			// turn off emulation mode
?>