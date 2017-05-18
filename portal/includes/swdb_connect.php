<?php 

// ---------------------------------------------------------------------------------
//  FILENAME:      swdb_connect.php
//
//  DESCRIPTION:   MYSQL Database Connection Script
//
//  NOTES:         This source script contains the code to access the database.  
//                 The code makes a connection to our database.  
//
//  COPYRIGHTS:    Copyright (c) Watermark 2016
//                 All Rights Reserved                             
//
//  HISTORY:
//
//    MM/DD/YY  WHO	    NOTES
// ---------------------------------------------------------------------------------
//    01/05/16  UJS     Created this file
// ---------------------------------------------------------------------------------

// --------------------------------------------------------------------------    	  	
// Use constants to define the database access information.
// --------------------------------------------------------------------------                        
DEFINE ('DB_USER', 'rojectos_admin');
DEFINE ('DB_PASSWORD', 'W@termark!');
DEFINE ('DB_HOST', 'localhost');
DEFINE ('DB_NAME', 'rojectos_oslo');

// --------------------------------------------------------------------------    	  	
// Attempt to connect to the specified database.  Die if we get an error.
// --------------------------------------------------------------------------    	  	
$dbc = @($GLOBALS["___mysqli_ston"] = mysqli_connect(DB_HOST,  DB_USER,  DB_PASSWORD)) OR die ('DB_ERROR: Could not connect to database: ' . ((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)) );

// --------------------------------------------------------------------------    	  	
// Select the database.
// --------------------------------------------------------------------------    	  	
@((bool)mysqli_query($GLOBALS["___mysqli_ston"], "USE " . constant('DB_NAME'))) OR die ('DB_ERROR: Could not select the database: ' . ((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)) );

// --------------------------------------------------------------------------    	  	
// Utility function used to escape the data properly.  This prevents both 
// SQL injection attacks and cross-site scripting attacks.
// --------------------------------------------------------------------------    	  	
function escape_data ($data) 
{
	// --------------------------------------------------------------------------    	  	
	// Check if Magic Quotes are enabled.
	// --------------------------------------------------------------------------    	  	
	if (ini_get('magic_quotes_gpc')) 
	{
		$data = stripslashes($data);
	}

	// --------------------------------------------------------------------------    	  		
	// Check for mysql_real_escape_string support.
	// --------------------------------------------------------------------------    	  	
	if (function_exists('mysqli_real_escape_string')) 
	{
		global $dbc; // Need the connection.
		$data = mysqli_real_escape_string( $dbc, trim($data));
	} 
	
	else 
	{
		$data = ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], trim($data)) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));
	}

	// --------------------------------------------------------------------------    	  	
	// Now deal with idiots who attempt to perform a cross-site scripting 
	// attack by filtering the string.
	// --------------------------------------------------------------------------    	  	
	$data = htmlentities ($data);

	// --------------------------------------------------------------------------    	  	
	// Return the filtered data string back to the caller.
	// --------------------------------------------------------------------------    	  	
	return $data;

} // End of function.
?>